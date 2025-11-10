<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Models\UserCart;
use App\Models\Promotion;
use App\Services\PaymentService;
use App\Services\Promotions\PromotionService;
use App\Events\OrderCreatedEvent;

class PaymentController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function index()
    {
        return view('customer.payment.index');
    }

    public function placeOrder(Request $request)
    {
        // Debug log
        logger()->info('Payment submission received', ['data' => $request->all()]);

        // Basic validation - allow id to be string or integer
        $validated = $request->validate([
            'cart' => 'required|array|min:1',
            'cart.*.id' => 'required|exists:menu_items,id',
            'cart.*.quantity' => 'required|integer|min:1',
            'cart.*.unit_price' => 'nullable|numeric|min:0', // For bundle/promo discounted prices
            'cart.*.payment_method' => 'nullable|string|in:online,counter',
            'cart.*.notes' => 'nullable|string|max:500', // Special notes/instructions
            // Add-ons tracking
            'cart.*.selectedAddons' => 'nullable|array',
            'cart.*.selectedAddons.*.id' => 'required|exists:menu_item_addons,id',
            'cart.*.selectedAddons.*.name' => 'required|string',
            'cart.*.selectedAddons.*.price' => 'required|numeric|min:0',
            // Promotion tracking fields
            'cart.*.promotion_id' => 'nullable|integer|exists:promotions,id',
            'cart.*.promotion_group_id' => 'nullable|string|max:100',
            'cart.*.is_free_item' => 'nullable|boolean',
            'cart.*.original_price' => 'nullable|numeric|min:0',
            // Reward tracking field for free item rewards
            'cart.*.customer_reward_id' => 'nullable|integer|exists:customer_rewards,id',
            'is_from_cart' => 'nullable|boolean',
            'payment_details' => 'required|array',
            'payment_details.method' => 'required|string|in:online,counter',
            'payment_details.order_type' => 'nullable|string|in:dine_in,takeaway',
            'payment_details.email' => 'nullable|email',
            'payment_details.name' => 'nullable|string',
            'payment_details.phone' => 'nullable|string',
            'promo_code' => 'nullable|string|max:50', // Promo code field
            'voucher_discount' => 'nullable|numeric|min:0', // Voucher discount amount
        ]);

        DB::beginTransaction();

        try {
            $user = Auth::user();
            $cartItems = $validated['cart'];
            $paymentDetails = $validated['payment_details'];
            $totalAmount = 0;

            // Validate and filter unavailable items
            $validCartItems = [];
            $skippedItems = [];

            foreach ($cartItems as $item) {
                $menuItem = \App\Models\MenuItem::find($item['id']);

                // Check if menu item exists and is available
                if (!$menuItem || $menuItem->trashed() || !$menuItem->availability) {
                    $skippedItems[] = [
                        'id' => $item['id'],
                        'name' => $menuItem ? $menuItem->name : 'Unknown item',
                        'reason' => !$menuItem ? 'Item tidak wujud' : ($menuItem->trashed() ? 'Item telah dikeluarkan' : 'Item tidak tersedia')
                    ];
                    continue;
                }

                // Item is valid, add to valid items and calculate total
                $validCartItems[] = $item;

                // Use unit_price from cart if available (for bundle/promo discounts), otherwise use menu item price
                $itemPrice = isset($item['unit_price']) ? floatval($item['unit_price']) : $menuItem->price;

                // Calculate add-ons total for this item
                $addonsTotal = 0;
                if (isset($item['selectedAddons']) && is_array($item['selectedAddons'])) {
                    foreach ($item['selectedAddons'] as $addon) {
                        $addonsTotal += floatval($addon['price']);
                    }
                }

                // Add item price + add-ons total, multiplied by quantity
                $totalAmount += ($itemPrice + $addonsTotal) * $item['quantity'];
            }

            // Check if we have any valid items to checkout
            if (empty($validCartItems)) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'error' => 'Tiada item yang sah untuk checkout. Semua item dalam cart tidak tersedia.',
                    'skipped_items' => $skippedItems
                ], 400);
            }

            // Use valid items only for order creation
            $cartItems = $validCartItems;

            // === PROMO CODE HANDLING WITH CHECKOUT PROTECTION ===
            $appliedPromotion = null;
            $promoDiscount = 0;
            $promoCode = $validated['promo_code'] ?? null;

            // IMPORTANT: Prevent double discount - check if cart has bundle/combo/buy1free1/item_discount items
            $hasBundleItems = false;
            foreach ($cartItems as $item) {
                if (isset($item['promotion_id']) && !empty($item['promotion_id'])) {
                    $hasBundleItems = true;
                    break;
                }
            }

            if ($promoCode) {
                // VALIDATION: Cannot use promo code if cart already has bundle/combo items
                if ($hasBundleItems) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'error' => 'Promo code cannot be used with bundle/combo items. Please remove bundle items first or proceed without promo code.',
                        'error_type' => 'double_discount_prevented'
                    ], 400);
                }

                $promotionService = app(PromotionService::class);
                $usageLogger = app(\App\Services\Promotions\PromotionUsageLogger::class);

                // Build cart items array for promotion service
                $cartItemsForPromo = [];
                foreach ($cartItems as $item) {
                    $menuItem = \App\Models\MenuItem::find($item['id']);
                    $cartItemsForPromo[$item['id']] = [
                        'item' => $menuItem,
                        'quantity' => $item['quantity'],
                        'price' => $menuItem->price
                    ];
                }

                // CRITICAL: Revalidate promo code with DB locking to prevent race conditions
                // This ensures that if 2 users try to use the last slot simultaneously,
                // only one will succeed
                $promotion = Promotion::where('promo_code', $promoCode)
                    ->lockForUpdate() // 🔒 Lock the row to prevent concurrent access
                    ->first();

                if (!$promotion) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'error' => 'Invalid promo code.'
                    ], 400);
                }

                // Revalidate: Check if user can still use this promotion
                // (limits may have been reached since they applied it in cart)
                $canUseResult = $usageLogger->canUserUsePromotion($promotion, $user?->id);

                if (!$canUseResult['can_use']) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'error' => $canUseResult['reason'],
                        'error_type' => $canUseResult['error_type'] ?? 'validation_failed'
                    ], 400);
                }

                // Validate and apply promo code
                $appliedPromotion = $promotionService->validatePromoCode($promoCode, $cartItemsForPromo, $user);

                if ($appliedPromotion) {
                    $discountResult = $promotionService->calculatePromotionDiscount($appliedPromotion, $cartItemsForPromo);
                    $promoDiscount = $discountResult['discount'];

                    logger()->info('Promo code applied at checkout', [
                        'promo_code' => $promoCode,
                        'discount' => $promoDiscount,
                        'promotion_id' => $appliedPromotion->id,
                        'remaining_uses' => $canUseResult['remaining_uses'] ?? 'unlimited'
                    ]);
                } else {
                    logger()->warning('Invalid promo code provided', ['promo_code' => $promoCode]);
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'error' => 'Promo code is invalid or does not meet requirements.'
                    ], 400);
                }
            }

            // Get voucher discount from request (applied in cart)
            $voucherDiscount = floatval($validated['voucher_discount'] ?? 0);

            // Calculate final total amount (after promo and voucher discounts)
            $subtotal = $totalAmount;
            $finalTotal = max(0, $totalAmount - $promoDiscount - $voucherDiscount);

            logger()->info('Order total calculation', [
                'subtotal' => $subtotal,
                'promo_discount' => $promoDiscount,
                'voucher_discount' => $voucherDiscount,
                'final_total' => $finalTotal
            ]);

            // Determine payment method and set initial status
            $paymentMethod = $paymentDetails['method'];
            $isCounterPayment = ($paymentMethod === 'counter');

            // For counter payment, create order immediately
            // For online payment, only create payment record first (order created after successful payment)
            if ($paymentMethod === 'counter') {
                // Get applied voucher info from session
                $appliedVoucher = session('applied_voucher');

                // Prepare order data
                $orderData = [
                    'user_id' => $user ? $user->id : null,
                    'guest_name' => $user ? $user->name : ($paymentDetails['name'] ?? 'Guest'),
                    'guest_phone' => $user ? $user->phone : ($paymentDetails['phone'] ?? null),
                    'guest_email' => $user ? $user->email : ($paymentDetails['email'] ?? null),
                    'total_amount' => $finalTotal, // Use final total after discount
                    'order_status' => 'pending',
                    'payment_status' => 'unpaid',
                    'payment_method' => $paymentMethod,
                    'order_type' => $paymentDetails['order_type'] ?? 'takeaway',
                    'order_source' => 'web',
                    'order_time' => now(),
                    // Voucher discount tracking
                    'customer_voucher_id' => $appliedVoucher['id'] ?? null,
                    'voucher_discount' => $voucherDiscount,
                    'voucher_code' => isset($appliedVoucher['id']) ? ($appliedVoucher['voucher_code'] ?? null) : null,
                    // confirmation_code will be auto-generated by Order model boot method
                ];

                logger()->info('Creating order with data:', $orderData);

                // Create the Order
                $order = Order::create($orderData);

                // DEBUG: Log cart items data before creating order items
                \Log::info('Payment Checkout - Cart Items Data', [
                    'order_id' => $order->id,
                    'total_items' => count($cartItems),
                    'cart_items' => collect($cartItems)->map(fn($item) => [
                        'id' => $item['id'],
                        'name' => $item['name'] ?? 'Unknown',
                        'quantity' => $item['quantity'],
                        'promotion_id' => $item['promotion_id'] ?? null,
                        'promotion_group_id' => $item['promotion_group_id'] ?? null,
                        'is_free_item' => $item['is_free_item'] ?? false,
                        'customer_reward_id' => $item['customer_reward_id'] ?? null,
                    ])
                ]);

                // Create OrderItems with promotion info
                foreach ($cartItems as $item) {
                    $menuItem = \App\Models\MenuItem::find($item['id']);

                    // Use unit_price from cart if available (for bundle/promo discounts), otherwise use menu item price
                    $itemPrice = isset($item['unit_price']) ? floatval($item['unit_price']) : $menuItem->price;
                    $itemTotal = $itemPrice * $item['quantity'];

                    // Calculate discount if unit_price is lower than original price
                    $discountAmount = ($menuItem->price - $itemPrice) * $item['quantity'];

                    // Check if item is part of promotion group (bundle/combo)
                    $promotionGroupId = isset($item['promotion_group_id']) ? $item['promotion_group_id'] : null;
                    $itemPromotionId = isset($item['promotion_id']) ? $item['promotion_id'] : ($appliedPromotion ? $appliedPromotion->id : null);
                    $isFreeItem = isset($item['is_free_item']) ? $item['is_free_item'] : false;
                    $customerRewardId = isset($item['customer_reward_id']) ? $item['customer_reward_id'] : null;

                    $orderItem = OrderItem::create([
                        'order_id' => $order->id,
                        'menu_item_id' => $item['id'],
                        'quantity' => $item['quantity'],
                        'unit_price' => $isFreeItem ? 0 : $itemPrice, // Free items get 0 price
                        'original_price' => $menuItem->price, // Always store original price
                        'total_price' => $isFreeItem ? 0 : $itemTotal,
                        'special_note' => $item['notes'] ?? null,
                        'item_status' => 'pending',
                        'promotion_id' => $itemPromotionId,
                        'customer_reward_id' => $customerRewardId, // Link to reward redemption
                        'is_combo_item' => $promotionGroupId ? true : false,  // Mark if part of combo
                        'combo_group_id' => $promotionGroupId, // Store promotion group ID as combo group ID
                        'discount_amount' => max(0, $discountAmount), // Discount from bundle/promo
                    ]);

                    // Save selected add-ons for this order item
                    if (isset($item['selectedAddons']) && is_array($item['selectedAddons'])) {
                        foreach ($item['selectedAddons'] as $addon) {
                            \App\Models\MenuCustomization::create([
                                'order_item_id' => $orderItem->id,
                                'customization_type' => 'addon',
                                'customization_value' => $addon['name'],
                                'additional_price' => $addon['price'],
                            ]);
                        }
                    }
                }

                // Log promotion usage if applied
                if ($appliedPromotion && $promoDiscount > 0) {
                    $promotionService = app(PromotionService::class);
                    $promotionService->applyPromotionToOrder(
                        $appliedPromotion,
                        $order->id,
                        $user ? $user->id : null,
                        $promoDiscount,
                        $subtotal,
                        $finalTotal
                    );

                    logger()->info('Promotion logged for order', [
                        'order_id' => $order->id,
                        'promotion_id' => $appliedPromotion->id,
                        'discount' => $promoDiscount
                    ]);
                }

                // Auto-create ETA based on order items
                $order->load('items.menuItem');
                if ($order->items->count() > 0) {
                    $order->autoCreateETA();
                }

                // 🔥 KITCHEN LOAD BALANCING: Auto-distribute order to stations
                // Distribute immediately so kitchen can start preparing
                if ($order->items->count() > 0 && !in_array($order->order_status, ['cancelled', 'completed'])) {
                    try {
                        $distributionService = app(\App\Services\Kitchen\OrderDistributionService::class);
                        $distributionService->distributeOrder($order->fresh()->load('items.menuItem.category.defaultStation', 'items.menuItem.stationOverride'));

                        logger()->info('✅ Counter order distributed to kitchen stations', [
                            'order_id' => $order->id,
                            'confirmation_code' => $order->confirmation_code,
                            'items_count' => $order->items->count()
                        ]);
                    } catch (\Exception $e) {
                        logger()->error('❌ Failed to distribute counter order to kitchen', [
                            'order_id' => $order->id,
                            'error' => $e->getMessage(),
                            'trace' => $e->getTraceAsString()
                        ]);
                        // Don't fail the order creation, just log the error
                    }
                }

                // Fire OrderCreatedEvent to notify admin
                event(new OrderCreatedEvent($order->fresh(['user', 'items'])));

                // Create counter payment record
                $paymentData = [
                    'payment_method' => 'counter',
                    'amount' => $finalTotal, // Use final total after discount
                    'currency' => 'MYR',
                    'payment_status' => 'pending',
                    'gateway' => 'manual',
                ];

                $payment = $this->paymentService->savePaymentData($paymentData, $order->id);

                // Award points to user if authenticated (1 point per RM1 spent)
                // AND update customer profile total_spent
                if ($user) { // Check if user is authenticated
                    // Calculate points (e.g., 1 point per RM1 spent)
                    $points = floor($finalTotal);

                    // Add points to user
                    $user->addPoints($points, 'Order #' . $order->confirmation_code . ' (Counter Payment)');

                    if ($user->customerProfile) {
                        $user->customerProfile->addSpending($finalTotal);
                        logger()->info('Customer profile total_spent updated (counter order)', [
                            'user_id' => $user->id,
                            'order_id' => $order->id,
                            'amount' => $finalTotal,
                            'new_total_spent' => $user->customerProfile->fresh()->total_spent
                        ]);
                    }
                }

                // Only clear user's cart if this order came from cart checkout
                $isFromCart = $validated['is_from_cart'] ?? false;
                if ($user && $isFromCart) {
                    UserCart::where('user_id', $user->id)->delete();
                }

                // Mark voucher as redeemed if used
                if ($user && $voucherDiscount > 0) {
                    $appliedVoucher = session('applied_voucher');
                    if ($appliedVoucher && isset($appliedVoucher['id'])) {
                        $voucher = \App\Models\CustomerVoucher::with('voucherTemplate')->find($appliedVoucher['id']);
                        if ($voucher) {
                            $voucher->status = 'redeemed';
                            $voucher->used_at = now();
                            $voucher->order_id = $order->id;
                            $voucher->save();

                            logger()->info('Voucher marked as redeemed', [
                                'voucher_id' => $voucher->id,
                                'order_id' => $order->id,
                                'discount' => $voucherDiscount,
                                'source' => $voucher->source
                            ]);

                            // If voucher came from reward redemption, mark the CustomerReward as redeemed too
                            if ($voucher->source === 'reward' && $voucher->voucherTemplate) {
                                $customerReward = \App\Models\CustomerReward::where('customer_profile_id', $voucher->customer_profile_id)
                                    ->whereHas('reward', function($query) use ($voucher) {
                                        $query->where('voucher_template_id', $voucher->voucher_template_id);
                                    })
                                    ->where('status', 'active')
                                    ->orderBy('created_at', 'desc')
                                    ->first();

                                if ($customerReward) {
                                    $customerReward->status = 'redeemed';
                                    $customerReward->redeemed_at = now();
                                    $customerReward->save();

                                    logger()->info('CustomerReward marked as redeemed via voucher usage', [
                                        'customer_reward_id' => $customerReward->id,
                                        'voucher_id' => $voucher->id,
                                        'order_id' => $order->id
                                    ]);
                                }
                            }

                            // Clear applied voucher from session
                            session()->forget('applied_voucher');
                        }
                    }
                }

                // Mark free item rewards as redeemed (product type rewards) - COUNTER ORDERS
                if ($user) {
                    // Load order items to check for free rewards
                    $order->load('items');
                    foreach ($order->items as $orderItem) {
                        // Check if this order item is linked to a reward redemption
                        if ($orderItem->customer_reward_id) {
                            $customerReward = \App\Models\CustomerReward::find($orderItem->customer_reward_id);
                            if ($customerReward && $customerReward->status !== 'redeemed') {
                                $customerReward->markAsRedeemed();

                                logger()->info('Free item reward marked as redeemed (counter order)', [
                                    'customer_reward_id' => $customerReward->id,
                                    'order_id' => $order->id,
                                    'menu_item_id' => $orderItem->menu_item_id,
                                    'reward_title' => $customerReward->reward->title ?? 'Unknown'
                                ]);
                            }
                        }
                    }
                }

                // Clear promo code from session if guest
                if (!$user) {
                    session()->forget(['guest_promo_code', 'guest_promo_discount']);
                }

                DB::commit();

                // Generate a unique order ID for display
                $displayOrderId = 'STG-' . $order->created_at->format('Ymd') . '-' . $order->id;

                $response = [
                    'success' => true,
                    'message' => 'Order placed successfully!',
                    'order_id' => $displayOrderId,
                    'amount' => $finalTotal,
                    'subtotal' => $subtotal,
                    'discount' => $promoDiscount,
                    'voucher_discount' => $voucherDiscount,
                    'promo_code' => $promoCode
                ];

                // Include info about skipped items if any
                if (!empty($skippedItems)) {
                    $response['warning'] = count($skippedItems) . ' item(s) tidak tersedia dan telah dikecualikan dari pesanan';
                    $response['skipped_items'] = $skippedItems;
                }

                return response()->json($response);

            } else {
                // Online payment - DO NOT create order yet, only create gateway payment
                // Order will be created in paymentCallback after successful payment

                // DEBUG: Log cart items before saving to session
                \Log::info('Saving cart items to session', [
                    'cart_items_count' => count($cartItems),
                    'cart_items' => $cartItems,
                    'has_promotion_fields' => collect($cartItems)->map(fn($item) => [
                        'id' => $item['id'],
                        'has_promotion_id' => isset($item['promotion_id']),
                        'has_promotion_group_id' => isset($item['promotion_group_id']),
                        'promotion_id' => $item['promotion_id'] ?? null,
                        'promotion_group_id' => $item['promotion_group_id'] ?? null,
                    ])
                ]);

                // Get applied voucher info from session
                $appliedVoucher = session('applied_voucher');

                // Store order data in session for later use after successful payment
                session([
                    'pending_order_data' => [
                        'user_id' => $user ? $user->id : null,
                        'guest_name' => $user ? $user->name : ($paymentDetails['name'] ?? 'Guest'),
                        'guest_phone' => $user ? $user->phone : ($paymentDetails['phone'] ?? null),
                        'guest_email' => $user ? $user->email : ($paymentDetails['email'] ?? null),
                        'total_amount' => $finalTotal, // Use final total after discount
                        'subtotal' => $subtotal,
                        'promo_discount' => $promoDiscount,
                        'voucher_discount' => $voucherDiscount, // Store voucher discount
                        'voucher_code' => $appliedVoucher ? ($appliedVoucher['voucher_code'] ?? null) : null,
                        'customer_voucher_id' => $appliedVoucher ? ($appliedVoucher['id'] ?? null) : null,
                        'promo_code' => $promoCode,
                        'promotion_id' => $appliedPromotion ? $appliedPromotion->id : null,
                        'order_status' => 'pending',
                        'payment_status' => 'paid',
                        'payment_method' => $paymentMethod,
                        'order_type' => $paymentDetails['order_type'] ?? 'takeaway',
                        'order_source' => 'web',
                        'order_time' => now(),
                        // DO NOT store confirmation_code in session - let Order model generate it
                        'cart_items' => $cartItems,
                        'is_from_cart' => $validated['is_from_cart'] ?? false,
                    ]
                ]);

                // Create gateway payment with temporary order reference
                $paymentData = [
                    'payment_method' => 'online',
                    'amount' => $finalTotal, // Use final total after discount
                    'currency' => 'MYR',
                    'customer_name' => $user ? $user->name : 'Guest',
                    'customer_email' => $paymentDetails['email'] ?? ($user ? $user->email : ''),
                    'customer_phone' => $user ? $user->phone_number : '',
                ];

                // Create payment without order_id (will be linked later)
                $gatewayResult = $this->paymentService->createGatewayPaymentWithoutOrder($paymentData);

                if (!$gatewayResult['success']) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => $gatewayResult['message']
                    ], 400);
                }

                // Store payment ID in session to link with order later
                session(['pending_payment_id' => $gatewayResult['payment_id']]);

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Redirecting to payment gateway...',
                    'redirect_url' => $gatewayResult['redirect_url'],
                    'payment_method' => 'gateway'
                ]);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            // Log the exception with full details
            logger()->error('Order placement failed: ' . $e->getMessage(), [
                'exception' => $e,
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while placing your order. Please try again.',
                'debug' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Handle payment return from gateway (customer redirected back)
     */
    public function paymentReturn($paymentId)
    {
        try {
            logger()->info('Payment return accessed', ['payment_id' => $paymentId]);

            $payment = \App\Models\Payment::findOrFail($paymentId);

            logger()->info('Payment found', [
                'payment_id' => $payment->id,
                'bill_code' => $payment->bill_code,
                'current_status' => $payment->payment_status,
                'has_order' => $payment->order_id ? 'yes' : 'no'
            ]);

            // Get latest bill status from Toyyibpay
            $toyyibpayService = new \App\Services\ToyyibpayService();
            $billStatus = $toyyibpayService->getBillStatus($payment->bill_code);

            logger()->info('Bill status retrieved', [
                'bill_code' => $payment->bill_code,
                'status_result' => $billStatus
            ]);

            if ($billStatus['success']) {
                // Update payment status based on current status
                $updateResult = $this->paymentService->updatePaymentStatus(
                    $payment->transaction_id,
                    $billStatus['status'],
                    $billStatus['response']
                );

                logger()->info('Payment status updated', [
                    'transaction_id' => $payment->transaction_id,
                    'new_status' => $billStatus['status'],
                    'update_result' => $updateResult ? 'success' : 'failed'
                ]);

                if ($billStatus['status'] === 'success') {
                    // Payment successful - check if order exists
                    if (!$payment->order_id) {
                        // Order not created yet (callback might not have been called)
                        // Create order from session data as fallback
                        $pendingOrderData = session('pending_order_data');

                        if ($pendingOrderData) {
                            logger()->info('Creating order from paymentReturn (callback fallback)', [
                                'payment_id' => $payment->id,
                                'pending_data' => $pendingOrderData
                            ]);

                            // Create the order (confirmation_code will be auto-generated by Order model)
                            $order = Order::create([
                                'user_id' => $pendingOrderData['user_id'],
                                'guest_name' => $pendingOrderData['guest_name'],
                                'guest_phone' => $pendingOrderData['guest_phone'],
                                'guest_email' => $pendingOrderData['guest_email'],
                                'total_amount' => $pendingOrderData['total_amount'],
                                'order_status' => $pendingOrderData['order_status'],
                                'payment_status' => 'paid',
                                'payment_method' => $pendingOrderData['payment_method'],
                                'order_type' => $pendingOrderData['order_type'],
                                'order_source' => $pendingOrderData['order_source'],
                                'order_time' => $pendingOrderData['order_time'],
                                // Voucher discount tracking
                                'customer_voucher_id' => $pendingOrderData['customer_voucher_id'] ?? null,
                                'voucher_discount' => $pendingOrderData['voucher_discount'] ?? 0,
                                'voucher_code' => $pendingOrderData['voucher_code'] ?? null,
                            ]);

                            // Create OrderItems
                            $hasPromotion = !empty($pendingOrderData['promotion_id']);
                            foreach ($pendingOrderData['cart_items'] as $item) {
                                $menuItem = \App\Models\MenuItem::find($item['id']);

                                // Use unit_price from cart if available (for bundle/promo pricing)
                                $itemPrice = isset($item['unit_price']) ? floatval($item['unit_price']) : $menuItem->price;
                                $itemTotal = $itemPrice * $item['quantity'];

                                // Get promotion tracking fields from cart item
                                $promotionGroupId = isset($item['promotion_group_id']) ? $item['promotion_group_id'] : null;
                                $itemPromotionId = isset($item['promotion_id']) ? $item['promotion_id'] : ($pendingOrderData['promotion_id'] ?? null);
                                $isFreeItem = isset($item['is_free_item']) ? $item['is_free_item'] : false;
                                $customerRewardId = isset($item['customer_reward_id']) ? $item['customer_reward_id'] : null;

                                // Calculate discount
                                $discountAmount = ($menuItem->price - $itemPrice) * $item['quantity'];

                                \App\Models\OrderItem::create([
                                    'order_id' => $order->id,
                                    'menu_item_id' => $item['id'],
                                    'quantity' => $item['quantity'],
                                    'unit_price' => $isFreeItem ? 0 : $itemPrice, // Free items get 0 price
                                    'original_price' => $menuItem->price,
                                    'total_price' => $isFreeItem ? 0 : $itemTotal,
                                    'special_note' => $item['notes'] ?? null,
                                    'item_status' => 'pending',
                                    'promotion_id' => $itemPromotionId,
                                    'customer_reward_id' => $customerRewardId, // Link to reward redemption
                                    'is_combo_item' => $promotionGroupId ? true : false,  // Mark if part of combo
                                    'combo_group_id' => $promotionGroupId, // Store promotion group ID as combo group ID
                                    'discount_amount' => max(0, $discountAmount),
                                ]);
                            }

                            // Log promotion usage if applied
                            if ($hasPromotion && $pendingOrderData['promo_discount'] > 0) {
                                $promotion = Promotion::find($pendingOrderData['promotion_id']);
                                if ($promotion) {
                                    $promotionService = app(PromotionService::class);
                                    $promotionService->applyPromotionToOrder(
                                        $promotion,
                                        $order->id,
                                        $pendingOrderData['user_id'],
                                        $pendingOrderData['promo_discount'],
                                        $pendingOrderData['subtotal'],
                                        $pendingOrderData['total_amount']
                                    );
                                }
                            }

                            // Auto-create ETA
                            $order->load('items.menuItem');
                            if ($order->items->count() > 0) {
                                $order->autoCreateETA();
                            }

                            // 🔥 KITCHEN LOAD BALANCING: Auto-distribute order to stations
                            // Distribute immediately so kitchen can start preparing
                            if ($order->items->count() > 0 && !in_array($order->order_status, ['cancelled', 'completed'])) {
                                try {
                                    $distributionService = app(\App\Services\Kitchen\OrderDistributionService::class);
                                    $distributionService->distributeOrder($order->fresh()->load('items.menuItem.category.defaultStation', 'items.menuItem.stationOverride'));

                                    logger()->info('✅ Payment return order distributed to kitchen stations', [
                                        'order_id' => $order->id,
                                        'confirmation_code' => $order->confirmation_code,
                                        'items_count' => $order->items->count()
                                    ]);
                                } catch (\Exception $e) {
                                    logger()->error('❌ Failed to distribute payment return order to kitchen', [
                                        'order_id' => $order->id,
                                        'error' => $e->getMessage(),
                                        'trace' => $e->getTraceAsString()
                                    ]);
                                    // Don't fail the order creation, just log the error
                                }
                            }

                            // Fire OrderCreatedEvent to notify admin
                            event(new OrderCreatedEvent($order->fresh(['user', 'orderItems'])));

                            // Link payment to order
                            $payment->update(['order_id' => $order->id]);

                            // Award points to user if authenticated
                            // AND update customer profile total_spent
                            if ($pendingOrderData['user_id']) {
                                $user = User::find($pendingOrderData['user_id']);
                                if ($user) {
                                    $points = floor($pendingOrderData['total_amount']);
                                    $user->addPoints($points, 'Order #' . $order->confirmation_code);

                                    // Update customer profile total spending
                                    if ($user->customerProfile) {
                                        $user->customerProfile->addSpending($pendingOrderData['total_amount']);
                                        logger()->info('Customer profile total_spent updated (payment return)', [
                                            'user_id' => $user->id,
                                            'order_id' => $order->id,
                                            'amount' => $pendingOrderData['total_amount'],
                                            'new_total_spent' => $user->customerProfile->fresh()->total_spent
                                        ]);
                                    }
                                }
                            }

                            // Clear user cart if from cart checkout
                            if ($pendingOrderData['is_from_cart'] && $pendingOrderData['user_id']) {
                                \App\Models\UserCart::where('user_id', $pendingOrderData['user_id'])->delete();
                            }

                            // Mark voucher as redeemed if used (for online payment)
                            if ($pendingOrderData['user_id'] && isset($pendingOrderData['voucher_discount']) && $pendingOrderData['voucher_discount'] > 0) {
                                $appliedVoucher = session('applied_voucher');
                                if ($appliedVoucher && isset($appliedVoucher['id'])) {
                                    $voucher = \App\Models\CustomerVoucher::with('voucherTemplate')->find($appliedVoucher['id']);
                                    if ($voucher) {
                                        // PHASE 2.4: redeemed_at removed, use used_at only
                                        $voucher->status = 'redeemed';
                                        $voucher->used_at = now();
                                        $voucher->order_id = $order->id;
                                        $voucher->save();

                                        logger()->info('Voucher marked as redeemed (online payment)', [
                                            'voucher_id' => $voucher->id,
                                            'order_id' => $order->id,
                                            'discount' => $pendingOrderData['voucher_discount'],
                                            'source' => $voucher->source
                                        ]);

                                        // If voucher came from reward redemption, mark the CustomerReward as redeemed too
                                        if ($voucher->source === 'reward' && $voucher->voucherTemplate) {
                                            $customerReward = \App\Models\CustomerReward::where('customer_profile_id', $voucher->customer_profile_id)
                                                ->whereHas('reward', function($query) use ($voucher) {
                                                    $query->where('voucher_template_id', $voucher->voucher_template_id);
                                                })
                                                ->where('status', 'active')
                                                ->orderBy('created_at', 'desc')
                                                ->first();

                                            if ($customerReward) {
                                                $customerReward->status = 'redeemed';
                                                $customerReward->redeemed_at = now();
                                                $customerReward->save();

                                                logger()->info('CustomerReward marked as redeemed via voucher usage (online payment)', [
                                                    'customer_reward_id' => $customerReward->id,
                                                    'voucher_id' => $voucher->id,
                                                    'order_id' => $order->id
                                                ]);
                                            }
                                        }
                                    }
                                }
                            }

                            // Mark free item rewards as redeemed (product type rewards)
                            if ($pendingOrderData['user_id']) {
                                // Load order items to check for free rewards
                                $order->load('items');
                                foreach ($order->items as $orderItem) {
                                    // Check if this order item is linked to a reward redemption
                                    if ($orderItem->customer_reward_id) {
                                        $customerReward = \App\Models\CustomerReward::find($orderItem->customer_reward_id);
                                        if ($customerReward && $customerReward->status !== 'redeemed') {
                                            $customerReward->markAsRedeemed();

                                            logger()->info('Free item reward marked as redeemed (online payment)', [
                                                'customer_reward_id' => $customerReward->id,
                                                'order_id' => $order->id,
                                                'menu_item_id' => $orderItem->menu_item_id,
                                                'reward_title' => $customerReward->reward->title ?? 'Unknown'
                                            ]);
                                        }
                                    }
                                }
                            }

                            // Clear session data
                            session()->forget(['pending_order_data', 'pending_payment_id', 'applied_voucher']);

                            logger()->info('Order created successfully from paymentReturn', [
                                'order_id' => $order->id,
                                'confirmation_code' => $order->confirmation_code
                            ]);
                        }
                    }

                    return redirect()->route('customer.orders.index')
                        ->with('success', 'Payment completed successfully! Your order is being prepared.');
                } elseif ($billStatus['status'] === 'failed' || $billStatus['status'] === 'pending') {
                    // Payment failed or cancelled - clean up

                    // Delete order if it exists (shouldn't for new flow, but keep for safety)
                    if ($payment->order) {
                        $payment->order->update(['order_status' => 'cancelled']);
                        $payment->order->delete(); // Soft delete
                    }

                    // Delete payment record
                    $payment->delete(); // Soft delete

                    // Clear session data if exists
                    session()->forget(['pending_order_data', 'pending_payment_id']);

                    return redirect()->route('customer.orders.index')
                        ->with('info', 'Payment was not completed. No order was placed. You can order again from the menu.');
                } else {
                    // For any other status
                    return redirect()->route('customer.orders.index')
                        ->with('info', 'Payment is still processing. Please wait for confirmation.');
                }
            }

            logger()->warning('Bill status check failed', ['bill_status' => $billStatus]);

            // If we can't verify status, check if payment is still pending
            if ($payment->payment_status === 'pending') {
                // Delete order if it exists
                if ($payment->order) {
                    $payment->order->update(['order_status' => 'cancelled']);
                    $payment->order->delete();
                }

                // Delete payment
                $payment->delete();

                // Clear session data
                session()->forget(['pending_order_data', 'pending_payment_id']);

                return redirect()->route('customer.orders.index')
                    ->with('info', 'Payment was not completed. No order was placed. You can order again from the menu.');
            }

            return redirect()->route('customer.orders.index')
                ->with('warning', 'Unable to verify payment status. Please contact support if payment was made.');

        } catch (\Exception $e) {
            logger()->error('Payment return error', [
                'payment_id' => $paymentId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Clean up session data on error
            session()->forget(['pending_order_data', 'pending_payment_id']);

            // Likely the payment was cancelled/failed
            return redirect()->route('customer.orders.index')
                ->with('info', 'Payment was not completed. No order was placed. You can order again from the menu.');
        }
    }

    /**
     * Handle payment callback from gateway (webhook)
     */
    public function paymentCallback(Request $request)
    {
        try {
            $callbackData = $request->all();
            
            logger()->info('Payment callback received', ['data' => $callbackData]);
            
            $result = $this->paymentService->handleGatewayCallback($callbackData);
            
            if ($result['success']) {
                return response('OK', 200);
            }
            
            return response('FAILED', 400);
            
        } catch (\Exception $e) {
            logger()->error('Payment callback error', [
                'data' => $request->all(),
                'error' => $e->getMessage()
            ]);
            
            return response('ERROR', 500);
        }
    }
}
