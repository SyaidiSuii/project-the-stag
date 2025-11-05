<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\TableController;
use App\Http\Controllers\Admin\TableReservationController;
use App\Http\Controllers\Admin\TableLayoutConfigController;
use App\Http\Controllers\Admin\MenuItemController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\SaleAnalyticsController;
use App\Http\Controllers\Admin\PushNotificationController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\RoleManagementController;
use App\Http\Controllers\Admin\TableQrcodeController;
// PHASE 4: Refactored Rewards Controllers
use App\Http\Controllers\Admin\RewardsController; // Dashboard only
use App\Http\Controllers\Admin\RewardManagementController;
use App\Http\Controllers\Admin\VoucherManagementController;
use App\Http\Controllers\Admin\LoyaltyTierManagementController;
use App\Http\Controllers\Admin\AchievementManagementController;
use App\Http\Controllers\Admin\BonusChallengeManagementController;
use App\Http\Controllers\Admin\LoyaltySettingsController;
use App\Http\Controllers\Admin\RedemptionManagementController;
use App\Http\Controllers\Admin\LoyaltyMemberController;
use App\Http\Controllers\QR\MenuController as QRMenuController;
use App\Http\Controllers\QR\PaymentController as QRPaymentController;
use App\Http\Controllers\Admin\MenuCustomizationController;
use App\Http\Controllers\Admin\QuickReorderController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\PromotionController as AdminPromotionController;
use App\Http\Controllers\Admin\StockDashboardController;
use App\Http\Controllers\Admin\StockItemController;
use App\Http\Controllers\Admin\SupplierController;
use App\Http\Controllers\Admin\PurchaseOrderController;
use App\Http\Controllers\Admin\HomepageContentController;
use App\Http\Controllers\Customer\HomeController as CustomerHomeController;
use App\Http\Controllers\Customer\FoodController as CustomerFoodController;
use App\Http\Controllers\Customer\DrinksController as CustomerDrinksController;
use App\Http\Controllers\Customer\OrdersController as CustomerOrdersController;
use App\Http\Controllers\Customer\RewardsController as CustomerRewardsController;
use App\Http\Controllers\Customer\BookingController as CustomerBookingController;
use App\Http\Controllers\Customer\AccountController as CustomerAccountController;
use App\Http\Controllers\Customer\PaymentController as CustomerPaymentController;
use App\Http\Controllers\Customer\CartController as CustomerCartController;
use App\Http\Controllers\Customer\BookingPaymentController as CustomerBookingPaymentController;
use App\Http\Controllers\Customer\PromotionController as CustomerPromotionController;
// use App\Http\Controllers\Customer\ReviewController as CustomerReviewController; // DISABLED - Reviews/Ratings feature hidden

use Illuminate\Support\Facades\Mail;
use App\Mail\HappyBirthday;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// =============================================
// PUBLIC ROUTES (No Authentication Required)
// =============================================
// =============================================
// CUSTOMER ROUTES (Public Access)
// =============================================
Route::get('/', [CustomerHomeController::class, 'index'])->name('customer.index');
Route::get('/home', [CustomerHomeController::class, 'index'])->name('customer.home.index');


Route::prefix('customer')->name('customer.')->group(function () {
    Route::post('/feedback', [CustomerHomeController::class, 'storeFeedback'])->name('feedback.store');

    // Unified Menu Page
    Route::get('/menu', [\App\Http\Controllers\Customer\MenuController::class, 'index'])->name('menu.index');
    Route::get('/menu/fast-items', [\App\Http\Controllers\Customer\MenuController::class, 'fastItems'])->name('menu.fast-items');
    Route::get('/menu/data', [\App\Http\Controllers\Customer\MenuController::class, 'getMenuData'])->name('menu.data');
    Route::get('/menu/kitchen-status', [\App\Http\Controllers\Customer\MenuController::class, 'getKitchenStatus'])->name('menu.kitchen-status');

    // Legacy routes for backward compatibility (redirect to menu)
    Route::get('/food', function () {
        return redirect()->route('customer.menu.index');
    })->name('food.index');
    Route::get('/food/data', [\App\Http\Controllers\Customer\MenuController::class, 'getMenuData'])->name('food.data');
    Route::get('/drinks', function () {
        return redirect()->route('customer.menu.index');
    })->name('drinks.index');
    Route::get('/orders', [CustomerOrdersController::class, 'index'])->name('orders.index');
    Route::get('/orders/{orderId}', [CustomerOrdersController::class, 'show'])->name('orders.show');
    Route::get('/orders/{orderId}/details', [CustomerOrdersController::class, 'getOrderDetails'])->name('orders.details');
    Route::get('/orders/{orderId}/tracking', [CustomerOrdersController::class, 'getOrderTracking'])->name('orders.tracking');
    Route::post('/orders/{orderId}/cancel', [CustomerOrdersController::class, 'cancelOrder'])->name('orders.cancel');
    Route::post('/orders/booking/{reservationId}/cancel', [CustomerOrdersController::class, 'cancelBooking'])->name('orders.booking.cancel');
    Route::get('/orders/{orderId}/reorder', [CustomerOrdersController::class, 'getReorderDetails'])->name('orders.reorder');
    Route::post('/orders/{orderId}/add-to-cart', [CustomerOrdersController::class, 'addToCart'])->name('orders.addToCart');
    Route::get('/rewards', [CustomerRewardsController::class, 'index'])->name('rewards.index');
    Route::post('/rewards/redeem', [CustomerRewardsController::class, 'redeem'])->name('rewards.redeem');
    Route::post('/rewards/checkin', [CustomerRewardsController::class, 'checkin'])->name('rewards.checkin');
    Route::post('/rewards/collect-voucher', [CustomerRewardsController::class, 'collectVoucher'])->name('rewards.collectVoucher');
    Route::post('/rewards/claim-bonus-challenge', [CustomerRewardsController::class, 'claimBonusChallenge'])->name('rewards.claimBonusChallenge');

    // Cart Voucher Routes
    Route::get('/cart/available-vouchers', [CustomerCartController::class, 'getAvailableVouchers'])->name('cart.availableVouchers');
    Route::post('/cart/apply-voucher', [CustomerCartController::class, 'applyVoucher'])->name('cart.applyVoucher');
    Route::post('/cart/remove-voucher', [CustomerCartController::class, 'removeVoucher'])->name('cart.removeVoucher');
    Route::get('/cart/get-applied-voucher', [CustomerCartController::class, 'getAppliedVoucher'])->name('cart.getAppliedVoucher');

    Route::get('/booking', [CustomerBookingController::class, 'index'])->name('booking.index');
    Route::post('/booking/store', [CustomerBookingController::class, 'store'])->name('booking.store');
    Route::post('/booking/check-availability', [CustomerBookingController::class, 'checkAvailability'])->name('booking.check-availability');
    Route::get('/booking/history', [CustomerBookingController::class, 'history'])->name('booking.history');
    Route::post('/booking/{reservationId}/cancel', [CustomerBookingController::class, 'cancel'])->name('booking.cancel');
    Route::get('/booking/{orderId}/payment', [CustomerBookingPaymentController::class, 'index'])->name('booking.payment.index');
    Route::post('/booking/{orderId}/payment', [CustomerBookingPaymentController::class, 'processPayment'])->name('booking.payment.process');
    Route::get('/account', [CustomerAccountController::class, 'index'])->name('account.index');
    Route::post('/account/update', [CustomerAccountController::class, 'update'])->name('account.update');
    Route::post('/account/change-password', [CustomerAccountController::class, 'changePassword'])->name('account.change-password');
    Route::post('/account/forgot-password', [CustomerAccountController::class, 'forgotPassword'])->name('account.forgot-password');
    Route::delete('/account/delete', [CustomerAccountController::class, 'deleteAccount'])->name('account.delete');
    Route::get('/payment', [CustomerPaymentController::class, 'index'])->name('payment.index');
    Route::post('/payment/place-order', [CustomerPaymentController::class, 'placeOrder'])->name('payment.placeOrder');

    // Cart API routes
    Route::prefix('cart')->name('cart.')->group(function () {
        Route::get('/', [CustomerCartController::class, 'index'])->name('index');
        Route::post('/add', [CustomerCartController::class, 'addItem'])->name('add');
        Route::put('/update/{menuItemId}', [CustomerCartController::class, 'updateItem'])->name('update');
        Route::delete('/remove/{menuItemId}', [CustomerCartController::class, 'removeItem'])->name('remove');
        Route::delete('/clear', [CustomerCartController::class, 'clearCart'])->name('clear');
        Route::delete('/remove-unavailable', [CustomerCartController::class, 'removeUnavailableItems'])->name('remove-unavailable');
        Route::post('/merge', [CustomerCartController::class, 'mergeCart'])->name('merge');

        // Promo code routes
        Route::post('/promo/apply', [CustomerCartController::class, 'applyPromoCode'])->name('promo.apply');
        Route::delete('/promo/remove', [CustomerCartController::class, 'removePromoCode'])->name('promo.remove');
        Route::get('/promo/details', [CustomerCartController::class, 'getPromoCodeDetails'])->name('promo.details');

        // Add promotion to cart (combo/bundle/buy-x-free-y)
        Route::post('/add-promotion', [CustomerCartController::class, 'addPromotionToCart'])->name('add-promotion');

        // Remove entire promotion group from cart (locked promotion items)
        Route::delete('/promotion-group/{promotionGroupId}', [CustomerCartController::class, 'removePromotionGroup'])->name('promotion-group.remove');

        // Free product routes (from reward redemption)
        Route::get('/free-products', [CustomerCartController::class, 'getAvailableFreeProducts'])->name('free-products');
        Route::post('/add-free-product', [CustomerCartController::class, 'addFreeProduct'])->name('add-free-product');
    });

    // Promotions routes
    Route::prefix('promotions')->name('promotions.')->group(function () {
        Route::get('/', [CustomerPromotionController::class, 'index'])->name('index');
        Route::get('/type/{type}', [CustomerPromotionController::class, 'byType'])->name('by-type');
        Route::get('/{id}', [CustomerPromotionController::class, 'show'])->name('show');
        Route::get('/happy-hour/{id}', [CustomerPromotionController::class, 'showHappyHour'])->name('happy-hour');
        Route::post('/apply-promo', [CustomerPromotionController::class, 'applyPromoCode'])->name('apply-promo');
        Route::post('/remove-promo', [CustomerPromotionController::class, 'removePromoCode'])->name('remove-promo');
        Route::post('/best-promotion', [CustomerPromotionController::class, 'getBestPromotion'])->name('best-promotion');
        Route::get('/api/active-happy-hours', [CustomerPromotionController::class, 'activeHappyHours'])->name('api.active-happy-hours');
    });

    // Reviews & Ratings routes - DISABLED
    // Route::prefix('reviews')->name('reviews.')->group(function () {
    //     // View reviews
    //     Route::get('/my-reviews', [CustomerReviewController::class, 'myReviews'])->name('my-reviews');
    //     Route::get('/menu-item/{menuItemId}', [CustomerReviewController::class, 'showMenuItemReviews'])->name('menu-item');
    //
    //     // Submit reviews
    //     Route::post('/store', [CustomerReviewController::class, 'store'])->name('store');
    //     Route::post('/store-batch', [CustomerReviewController::class, 'storeBatch'])->name('store-batch');
    //
    //     // Update/Delete reviews
    //     Route::put('/{reviewId}', [CustomerReviewController::class, 'update'])->name('update');
    //     Route::delete('/{reviewId}', [CustomerReviewController::class, 'destroy'])->name('destroy');
    // });
});

// Payment Gateway Routes (Public - No Authentication Required)
Route::prefix('payment')->name('payment.')->group(function () {
    Route::get('return/{payment}', [CustomerPaymentController::class, 'paymentReturn'])->name('return');
    Route::post('callback', [CustomerPaymentController::class, 'paymentCallback'])->name('callback');
});

// Default homepage redirect
Route::get('/customer', function () {
    return redirect()->route('customer.index');
});

// Testing route for birthday email
Route::get('/send-birthday-email/{user}', function (User $user) {
    Mail::to($user->email)->send(new HappyBirthday($user));
    return response()->json([
        'message' => "Happy Birthday email sent to {$user->name}!",
        'email' => $user->email,
    ]);
});

// =============================================
// AUTHENTICATED ROUTES
// =============================================
Route::middleware(['auth', 'verified'])->group(function () {

    // ---------------------------------------------
    // DASHBOARD & PROFILE MANAGEMENT
    // ---------------------------------------------
    Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])
        ->name('dashboard')
        ->middleware(['role:admin|manager']);

    Route::get('/admin/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])
        ->name('admin.dashboard')
        ->middleware(['role:admin|manager']);

    // Redirect /admin → /admin/dashboard
    Route::get('/admin', function () {
        return redirect()->route('admin.dashboard');
    });

    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');
        Route::patch('customer-profile', [ProfileController::class, 'updateCustomerProfile'])->name('customer.update');
        Route::delete('photo', [ProfileController::class, 'deletePhoto'])->name('photo.delete');
    });

    // =============================================
    // USER MANAGEMENT SECTION (Non-Admin)
    // =============================================

    // Users Management (for managers/supervisors)
    Route::middleware(['permission:user.menu'])->group(function () {
        Route::resource('users', UserController::class)->except(['show']);
    });

    // =============================================
    // KITCHEN STAFF ROUTES (Admin & Manager & Kitchen Staff)
    // =============================================
    Route::prefix('admin')->name('admin.')->middleware(['role:admin|manager|kitchen_staff'])->group(function () {
        // ---------------------------------------------
        // KITCHEN STAFF ACCESSIBLE ROUTES
        // ---------------------------------------------
        Route::prefix('kitchen')->name('kitchen.')->group(function () {
            // KDS - Accessible by admin, manager, and kitchen_staff
            Route::get('/kds', [\App\Http\Controllers\Admin\KitchenController::class, 'kds'])->name('kds');

            // Orders - Accessible by admin, manager, and kitchen_staff
            Route::get('/orders', [\App\Http\Controllers\Admin\KitchenController::class, 'orders'])->name('orders');
            Route::get('/orders/{order}', [\App\Http\Controllers\Admin\KitchenController::class, 'orderDetail'])->name('orders.detail');
        });

        // ---------------------------------------------
        // ORDER STATUS MANAGEMENT (Kitchen Staff can update orders)
        // ---------------------------------------------
        Route::prefix('order')->name('order.')->group(function () {
            // Allow kitchen_staff to update order status (they need this for KDS)
            Route::post('{order}/update-status', [OrderController::class, 'updateStatus'])->name('updateStatus');
        });
    });

    // =============================================
    // ADMIN PANEL SECTION (Admin & Manager Only)
    // =============================================
    Route::prefix('admin')->name('admin.')->middleware(['role:admin|manager'])->group(function () {

        // Admin User Management (Full Access)
        Route::resource('user', UserController::class);

        // ---------------------------------------------
        // PERMISSIONS & ROLES MANAGEMENT
        // ---------------------------------------------
        Route::prefix('permissions')->name('permissions.')->group(function () {
            Route::get('api/list', [PermissionController::class, 'getPermissions'])->name('api.list');
        });
        Route::resource('permissions', PermissionController::class);

        Route::prefix('roles')->name('roles.')->group(function () {
            Route::get('assign', [RoleManagementController::class, 'assignForm'])->name('assign.form');
            Route::post('assign', [RoleManagementController::class, 'assignToUser'])->name('assign');
            Route::get('{role}/permissions', [RoleManagementController::class, 'getRolePermissions'])->name('permissions');
        });
        Route::resource('roles', RoleManagementController::class);

        // ---------------------------------------------
        // SETTINGS MANAGEMENT
        // ---------------------------------------------
        Route::prefix('settings')->name('settings.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\SettingsController::class, 'index'])->name('index');
            Route::post('admins', [\App\Http\Controllers\Admin\SettingsController::class, 'storeAdmin'])->name('admins.store');
            Route::put('admins/{id}', [\App\Http\Controllers\Admin\SettingsController::class, 'updateAdmin'])->name('admins.update');
            Route::delete('admins/{id}', [\App\Http\Controllers\Admin\SettingsController::class, 'deleteAdmin'])->name('admins.delete');
            Route::post('admins/{id}/toggle-super-admin', [\App\Http\Controllers\Admin\SettingsController::class, 'toggleSuperAdmin'])->name('admins.toggle-super-admin');
        });

        // ---------------------------------------------
        // HOMEPAGE CONTENT MANAGEMENT
        // ---------------------------------------------
        Route::prefix('homepage')->name('homepage.')->group(function () {
            Route::get('/', [HomepageContentController::class, 'index'])->name('index');
            Route::get('get-section/{sectionType}', [HomepageContentController::class, 'getSection'])->name('get-section');
            Route::post('/', [HomepageContentController::class, 'store'])->name('store');
            Route::post('upload-image', [HomepageContentController::class, 'uploadImage'])->name('upload-image');
            Route::put('{id}', [HomepageContentController::class, 'update'])->name('update');
            Route::delete('{id}', [HomepageContentController::class, 'destroy'])->name('destroy');
        });

        // ---------------------------------------------
        // KITCHEN MANAGEMENT (Admin/Manager Only)
        // ---------------------------------------------
        Route::prefix('kitchen')->name('kitchen.')->group(function () {
            // Kitchen Dashboard - Admin/Manager only
            Route::get('/', [\App\Http\Controllers\Admin\KitchenController::class, 'index'])->name('index');

            // Analytics - Admin/Manager only
            Route::get('/analytics', [\App\Http\Controllers\Admin\KitchenController::class, 'analytics'])->name('analytics');

            // Kitchen Stations
            Route::prefix('stations')->name('stations.')->group(function () {
                Route::get('/', [\App\Http\Controllers\Admin\StationTypeController::class, 'index'])->name('index');
                Route::get('/create', [\App\Http\Controllers\Admin\StationTypeController::class, 'create'])->name('form');
                Route::get('/{station}/edit', [\App\Http\Controllers\Admin\StationTypeController::class, 'edit'])->name('edit');
                Route::get('/{station}', [\App\Http\Controllers\Admin\StationTypeController::class, 'show'])->name('detail');
                Route::post('/', [\App\Http\Controllers\Admin\StationTypeController::class, 'store'])->name('store');
                Route::patch('/{station}/toggle-status', [\App\Http\Controllers\Admin\StationTypeController::class, 'toggleStatus'])->name('toggleStatus');
                Route::put('/{station}', [\App\Http\Controllers\Admin\StationTypeController::class, 'update'])->name('update');
                Route::delete('/{station}', [\App\Http\Controllers\Admin\StationTypeController::class, 'destroy'])->name('destroy');
            });

            // Station Types Management
            Route::prefix('station-types')->name('station-types.')->group(function () {
                Route::get('/', [\App\Http\Controllers\Admin\StationTypeController::class, 'index'])->name('index');
                Route::get('/create', [\App\Http\Controllers\Admin\StationTypeController::class, 'create'])->name('create');
                Route::post('/', [\App\Http\Controllers\Admin\StationTypeController::class, 'store'])->name('store');
                Route::get('/{stationType}/edit', [\App\Http\Controllers\Admin\StationTypeController::class, 'edit'])->name('edit');
                Route::put('/{stationType}', [\App\Http\Controllers\Admin\StationTypeController::class, 'update'])->name('update');
                Route::delete('/{stationType}', [\App\Http\Controllers\Admin\StationTypeController::class, 'destroy'])->name('destroy');
            });
        });

        // ---------------------------------------------
        // TABLE MANAGEMENT
        // ---------------------------------------------
        Route::patch('table/{table}/status', [TableController::class, 'updateStatus'])->name('table.update-status');
        Route::resource('table', TableController::class);

        // Table Reservations
        Route::prefix('table-reservation')->name('table-reservation.')->group(function () {
            Route::get('today', [TableReservationController::class, 'todayReservations'])->name('today');
            Route::post('{tableReservation}/update-status', [TableReservationController::class, 'updateStatus'])->name('update-status');
        });
        Route::resource('table-reservation', TableReservationController::class);

        // Table Layout Configuration
        Route::prefix('table-layout-config')->name('table-layout-config.')->group(function () {
            Route::post('{tableLayoutConfig}/toggle-status', [TableLayoutConfigController::class, 'toggleStatus'])->name('toggle-status');
            Route::post('{tableLayoutConfig}/duplicate', [TableLayoutConfigController::class, 'duplicate'])->name('duplicate');
        });

        Route::prefix('api/table-layouts')->name('api.table-layouts.')->group(function () {
            Route::get('active', [TableLayoutConfigController::class, 'getActiveLayouts'])->name('active');
            Route::get('{tableLayoutConfig}', [TableLayoutConfigController::class, 'getLayoutDetails'])->name('details');
            Route::get('statistics', [TableLayoutConfigController::class, 'getStatistics'])->name('statistics');
            Route::post('save-layout', [TableLayoutConfigController::class, 'saveLayout'])->name('save-layout');
            Route::post('add-table', [TableLayoutConfigController::class, 'addTable'])->name('add-table');
            Route::put('update-table/{tableId}', [TableLayoutConfigController::class, 'updateTable'])->name('update-table');
            Route::delete('delete-table/{tableId}', [TableLayoutConfigController::class, 'deleteTable'])->name('delete-table');
        });
        Route::resource('table-layout-config', TableLayoutConfigController::class);

        // Table QR Codes
        Route::prefix('table-qrcodes')->name('table-qrcodes.')->group(function () {
            Route::get('active', [TableQrcodeController::class, 'active'])->name('active');
            Route::post('{tableQrcode}/complete', [TableQrcodeController::class, 'complete'])->name('complete');
            Route::post('{tableQrcode}/extend', [TableQrcodeController::class, 'extend'])->name('extend');
            Route::post('{tableQrcode}/regenerate-qr', [TableQrcodeController::class, 'regenerateQR'])->name('regenerate-qr');
            Route::get('{tableQrcode}/qr-code', [TableQrcodeController::class, 'qrCode'])->name('qr-code');
            Route::get('{tableQrcode}/qr-download/{format}', [TableQrcodeController::class, 'downloadQR'])->name('download-qr')->where('format', 'png|svg');
            Route::get('{tableQrcode}/qr-preview/{format?}', [TableQrcodeController::class, 'previewQR'])->name('qr-preview');
            Route::get('{tableQrcode}/print', [TableQrcodeController::class, 'printQR'])->name('print');
            Route::post('expire-old', [TableQrcodeController::class, 'expireOldSessions'])->name('expire-old');
        });
        Route::resource('table-qrcodes', TableQrcodeController::class);

        // =============================================
        // LOYALTY & REWARDS SYSTEM (PHASE 7)
        // =============================================
        Route::prefix('rewards')->name('rewards.')->group(function () {

            // Main Dashboard - Overview of all loyalty components
            Route::get('/', [RewardsController::class, 'index'])->name('index');

            // ---------------------------------------------
            // REWARDS MANAGEMENT
            // ---------------------------------------------
            Route::prefix('rewards')->name('rewards.')->group(function () {
                Route::get('/', [RewardManagementController::class, 'index'])->name('index');
                Route::get('/create', [RewardManagementController::class, 'create'])->name('create');
                Route::post('/', [RewardManagementController::class, 'store'])->name('store');
                Route::get('/{reward}', [RewardManagementController::class, 'show'])->name('show');
                Route::get('/{reward}/edit', [RewardManagementController::class, 'edit'])->name('edit');
                Route::put('/{reward}', [RewardManagementController::class, 'update'])->name('update');
                Route::delete('/{reward}', [RewardManagementController::class, 'destroy'])->name('destroy');
                Route::patch('/{reward}/toggle', [RewardManagementController::class, 'toggleActive'])->name('toggle');
                Route::post('/{reward}/duplicate', [RewardManagementController::class, 'duplicate'])->name('duplicate');
            });

            // ---------------------------------------------
            // VOUCHER TEMPLATES MANAGEMENT
            // ---------------------------------------------
            Route::prefix('voucher-templates')->name('voucher-templates.')->group(function () {
                Route::get('/', [VoucherManagementController::class, 'indexTemplates'])->name('index');
                Route::get('/create', [VoucherManagementController::class, 'createTemplate'])->name('create');
                Route::post('/', [VoucherManagementController::class, 'storeTemplate'])->name('store');
                Route::get('/{template}', [VoucherManagementController::class, 'showTemplate'])->name('show');
                Route::get('/{template}/edit', [VoucherManagementController::class, 'editTemplate'])->name('edit');
                Route::put('/{template}', [VoucherManagementController::class, 'updateTemplate'])->name('update');
                Route::delete('/{template}', [VoucherManagementController::class, 'destroyTemplate'])->name('destroy');
                Route::post('/{template}/generate', [VoucherManagementController::class, 'generateVouchers'])->name('generate');
            });

            // ---------------------------------------------
            // VOUCHER COLLECTIONS MANAGEMENT
            // ---------------------------------------------
            Route::prefix('voucher-collections')->name('voucher-collections.')->group(function () {
                Route::get('/', [VoucherManagementController::class, 'indexCollections'])->name('index');
                Route::get('/create', [VoucherManagementController::class, 'createCollection'])->name('create');
                Route::post('/', [VoucherManagementController::class, 'storeCollection'])->name('store');
                Route::get('/{collection}/edit', [VoucherManagementController::class, 'editCollection'])->name('edit');
                Route::put('/{collection}', [VoucherManagementController::class, 'updateCollection'])->name('update');
                Route::delete('/{collection}', [VoucherManagementController::class, 'destroyCollection'])->name('destroy');
            });

            // ---------------------------------------------
            // LOYALTY TIERS MANAGEMENT
            // ---------------------------------------------
            Route::prefix('loyalty-tiers')->name('loyalty-tiers.')->group(function () {
                Route::get('/', [LoyaltyTierManagementController::class, 'index'])->name('index');
                Route::get('/create', [LoyaltyTierManagementController::class, 'create'])->name('create');
                Route::post('/', [LoyaltyTierManagementController::class, 'store'])->name('store');
                Route::get('/{tier}', [LoyaltyTierManagementController::class, 'show'])->name('show');
                Route::get('/{tier}/edit', [LoyaltyTierManagementController::class, 'edit'])->name('edit');
                Route::put('/{tier}', [LoyaltyTierManagementController::class, 'update'])->name('update');
                Route::delete('/{tier}', [LoyaltyTierManagementController::class, 'destroy'])->name('destroy');
                Route::patch('/{tier}/toggle', [LoyaltyTierManagementController::class, 'toggleActive'])->name('toggle');
                Route::post('/reorder', [LoyaltyTierManagementController::class, 'reorder'])->name('reorder');
            });

            // ---------------------------------------------
            // ACHIEVEMENTS MANAGEMENT
            // ---------------------------------------------
            Route::prefix('achievements')->name('achievements.')->group(function () {
                Route::get('/', [AchievementManagementController::class, 'index'])->name('index');
                Route::get('/create', [AchievementManagementController::class, 'create'])->name('create');
                Route::post('/', [AchievementManagementController::class, 'store'])->name('store');
                Route::get('/{achievement}/edit', [AchievementManagementController::class, 'edit'])->name('edit');
                Route::put('/{achievement}', [AchievementManagementController::class, 'update'])->name('update');
                Route::delete('/{achievement}', [AchievementManagementController::class, 'destroy'])->name('destroy');
            });

            // ---------------------------------------------
            // BONUS CHALLENGES MANAGEMENT
            // ---------------------------------------------
            Route::prefix('bonus-challenges')->name('bonus-challenges.')->group(function () {
                Route::get('/', [BonusChallengeManagementController::class, 'index'])->name('index');
                Route::get('/create', [BonusChallengeManagementController::class, 'create'])->name('create');
                Route::post('/', [BonusChallengeManagementController::class, 'store'])->name('store');
                Route::get('/{challenge}/edit', [BonusChallengeManagementController::class, 'edit'])->name('edit');
                Route::put('/{challenge}', [BonusChallengeManagementController::class, 'update'])->name('update');
                Route::delete('/{challenge}', [BonusChallengeManagementController::class, 'destroy'])->name('destroy');
            });

            // ---------------------------------------------
            // CHECK-IN SETTINGS
            // ---------------------------------------------
            Route::prefix('checkin')->name('checkin.')->group(function () {
                Route::get('/', [LoyaltySettingsController::class, 'indexCheckin'])->name('index');
                Route::post('/', [LoyaltySettingsController::class, 'updateCheckin'])->name('update');
            });

            // ---------------------------------------------
            // SPECIAL EVENTS MANAGEMENT
            // ---------------------------------------------
            Route::prefix('special-events')->name('special-events.')->group(function () {
                Route::get('/', [LoyaltySettingsController::class, 'indexEvents'])->name('index');
                Route::get('/create', [LoyaltySettingsController::class, 'createEvent'])->name('create');
                Route::post('/', [LoyaltySettingsController::class, 'storeEvent'])->name('store');
                Route::get('/{event}/edit', [LoyaltySettingsController::class, 'editEvent'])->name('edit');
                Route::put('/{event}', [LoyaltySettingsController::class, 'updateEvent'])->name('update');
                Route::delete('/{event}', [LoyaltySettingsController::class, 'destroyEvent'])->name('destroy');
                Route::patch('/{event}/toggle', [LoyaltySettingsController::class, 'toggleEvent'])->name('toggle');
            });

            // ---------------------------------------------
            // CONTENT SETTINGS
            // ---------------------------------------------
            Route::prefix('content')->name('content.')->group(function () {
                Route::get('/', [LoyaltySettingsController::class, 'indexContent'])->name('index');
                Route::post('/', [LoyaltySettingsController::class, 'updateContent'])->name('update');
            });

            // ---------------------------------------------
            // REDEMPTIONS MANAGEMENT
            // ---------------------------------------------
            Route::prefix('redemptions')->name('redemptions.')->group(function () {
                Route::get('/', [RedemptionManagementController::class, 'index'])->name('index');
                Route::get('/{redemption}', [RedemptionManagementController::class, 'show'])->name('show');
                Route::post('/{redemption}/mark-redeemed', [RedemptionManagementController::class, 'markAsRedeemed'])->name('mark-redeemed');
                Route::post('/{redemption}/cancel', [RedemptionManagementController::class, 'cancel'])->name('cancel');
                Route::get('/export/csv', [RedemptionManagementController::class, 'exportCSV'])->name('export');
            });

            // ---------------------------------------------
            // LOYALTY MEMBERS MANAGEMENT
            // ---------------------------------------------
            Route::prefix('members')->name('members.')->group(function () {
                Route::get('/', [LoyaltyMemberController::class, 'index'])->name('index');
                Route::get('/{member}', [LoyaltyMemberController::class, 'show'])->name('show');
                Route::post('/{member}/adjust-points', [LoyaltyMemberController::class, 'adjustPoints'])->name('adjust-points');
                Route::post('/{member}/reset-points', [LoyaltyMemberController::class, 'resetPoints'])->name('reset-points');
                Route::get('/export/csv', [LoyaltyMemberController::class, 'exportCSV'])->name('export');
            });
        });

        // ---------------------------------------------
        // MENU MANAGEMENT
        // ---------------------------------------------
        Route::prefix('categories')->name('categories.')->group(function () {
            Route::get('{id}/restore', [CategoryController::class, 'restore'])->name('restore')->withTrashed();
            Route::delete('{id}/force-delete', [CategoryController::class, 'forceDelete'])->name('force-delete')->withTrashed();
            Route::get('subcategories', [CategoryController::class, 'getSubCategories'])->name('subcategories');
            Route::post('sort-order', [CategoryController::class, 'updateSortOrder'])->name('sort-order');
            Route::get('hierarchical', [CategoryController::class, 'getHierarchical'])->name('hierarchical');
        });
        Route::resource('categories', CategoryController::class);

        Route::prefix('menu-items')->name('menu-items.')->group(function () {
            Route::get('featured', [MenuItemController::class, 'getFeatured'])->name('featured');
            Route::get('stats', [MenuItemController::class, 'getStatistics'])->name('stats');
            Route::get('sub-categories', [MenuItemController::class, 'getSubCategories'])->name('sub-categories');
            Route::patch('{menuItem}/toggle-availability', [MenuItemController::class, 'toggleAvailability'])->name('toggle-availability');
            Route::patch('{menuItem}/toggle-featured', [MenuItemController::class, 'toggleFeatured'])->name('toggle-featured');
            Route::patch('{menuItem}/rating', [MenuItemController::class, 'updateRating'])->name('rating');

            // Routes for Set Meals (must be before resource route)
            Route::get('/create-set-meal', [MenuItemController::class, 'createSetMeal'])->name('create-set-meal');
            Route::post('/store-set-meal', [MenuItemController::class, 'storeSetMeal'])->name('store-set-meal');
        });
        Route::resource('menu-items', MenuItemController::class);

        // Menu Customizations
        Route::prefix('menu-customizations')->name('menu-customizations.')->group(function () {
            Route::get('export', [MenuCustomizationController::class, 'export'])->name('export');
            Route::get('by-order-item/{orderItem}', [MenuCustomizationController::class, 'getByOrderItem'])->name('by-order-item');
            Route::post('bulk-delete', [MenuCustomizationController::class, 'bulkDelete'])->name('bulkDelete');
            Route::get('get-customizations', [MenuCustomizationController::class, 'getCustomizationsByOrderItem'])->name('get-customizations');
        });
        Route::resource('menu-customizations', MenuCustomizationController::class);

        // ---------------------------------------------
        // ORDER MANAGEMENT
        // ---------------------------------------------
        Route::prefix('order')->name('order.')->group(function () {
            Route::get('today', [OrderController::class, 'today'])->name('today');
            // NOTE: update-status route moved to kitchen staff group above (line 260)
            Route::post('{order}/update-payment-status', [OrderController::class, 'updatePaymentStatus'])->name('updatePaymentStatus');
            Route::get('by-status', [OrderController::class, 'getByStatus'])->name('getByStatus');
            Route::post('{order}/cancel', [OrderController::class, 'cancel'])->name('cancel');
            Route::get('{order}/duplicate', [OrderController::class, 'duplicate'])->name('duplicate');
            Route::post('calculate-eta', [OrderController::class, 'calculateETA'])->name('calculateETA');
            Route::get('menu-item-prep-time', [OrderController::class, 'getMenuItemPrepTime'])->name('getMenuItemPrepTime');
        });
        Route::resource('order', OrderController::class);




        // Quick Reorder
        Route::prefix('quick-reorder')->name('quick-reorder.')->group(function () {
            Route::get('popular', [QuickReorderController::class, 'getPopular'])->name('getPopular');
            Route::get('recent', [QuickReorderController::class, 'getRecent'])->name('getRecent');
            Route::get('by-customer', [QuickReorderController::class, 'getByCustomer'])->name('getByCustomer');
            Route::get('search', [QuickReorderController::class, 'search'])->name('search');
            Route::post('{quickReorder}/convert-to-order', [QuickReorderController::class, 'convertToOrder'])->name('convertToOrder');
            Route::get('{quickReorder}/duplicate', [QuickReorderController::class, 'duplicate'])->name('duplicate');
            Route::post('{quickReorder}/update-frequency', [QuickReorderController::class, 'updateFrequency'])->name('updateFrequency');
            Route::post('bulk-delete', [QuickReorderController::class, 'bulkDelete'])->name('bulkDelete');
        });
        Route::resource('quick-reorder', QuickReorderController::class);

        // ---------------------------------------------
        // ANALYTICS & REPORTING
        // ---------------------------------------------
        Route::get('reports', [\App\Http\Controllers\Admin\ReportController::class, 'index'])->name('reports.index');
        Route::get('reports/generate-pdf', [\App\Http\Controllers\Admin\ReportController::class, 'generatePDF'])->name('reports.generate-pdf');
        Route::get('reports/download-pdf', [\App\Http\Controllers\Admin\ReportController::class, 'downloadPDF'])->name('reports.download-pdf');
        Route::get('reports/live-analytics', [\App\Http\Controllers\Admin\ReportController::class, 'getLiveAnalytics'])->name('reports.live-analytics');
        Route::get('reports/chart-data', [\App\Http\Controllers\Admin\ReportController::class, 'getChartData'])->name('reports.chart-data');

        Route::prefix('sale-analytics')->name('sale-analytics.')->group(function () {
            Route::get('dashboard-stats', [SaleAnalyticsController::class, 'getDashboardStats'])->name('dashboard-stats');
            Route::get('date-range', [SaleAnalyticsController::class, 'getDateRangeAnalytics'])->name('date-range');
            Route::post('generate/{date?}', [SaleAnalyticsController::class, 'generateDailyAnalytics'])->name('generate');
            Route::get('popular-items', [SaleAnalyticsController::class, 'getPopularItems'])->name('popular-items');
            Route::get('peak-hours', [SaleAnalyticsController::class, 'getPeakHours'])->name('peak-hours');
            Route::get('customer-analytics', [SaleAnalyticsController::class, 'getCustomerAnalytics'])->name('customer-analytics');
            Route::get('trends', [SaleAnalyticsController::class, 'getTrends'])->name('trends');
        });
        Route::resource('sale-analytics', SaleAnalyticsController::class);

        // ---------------------------------------------
        // PROMOTIONS MANAGEMENT
        // ---------------------------------------------
        Route::prefix('promotions')->name('promotions.')->group(function () {
            Route::get('/', [AdminPromotionController::class, 'index'])->name('index');
            Route::get('/create', [AdminPromotionController::class, 'create'])->name('create');
            Route::post('/', [AdminPromotionController::class, 'store'])->name('store');
            Route::get('/{promotion}', [AdminPromotionController::class, 'show'])->name('show');
            Route::get('/{promotion}/edit', [AdminPromotionController::class, 'edit'])->name('edit');
            Route::put('/{promotion}', [AdminPromotionController::class, 'update'])->name('update');
            Route::delete('/{promotion}', [AdminPromotionController::class, 'destroy'])->name('destroy');
            Route::post('/{promotion}/toggle-status', [AdminPromotionController::class, 'toggleStatus'])->name('toggle-status');

            // New promotion actions
            Route::get('/{promotion}/stats', [AdminPromotionController::class, 'stats'])->name('stats');
            Route::get('/{promotion}/analytics', [AdminPromotionController::class, 'analytics'])->name('analytics');
            Route::post('/{promotion}/duplicate', [AdminPromotionController::class, 'duplicate'])->name('duplicate');

            // Happy Hour Deals
            Route::get('/happy-hour/create', [AdminPromotionController::class, 'createHappyHour'])->name('happy-hour.create');
            Route::post('/happy-hour', [AdminPromotionController::class, 'storeHappyHour'])->name('happy-hour.store');
            Route::get('/happy-hour/{happyHourDeal}/edit', [AdminPromotionController::class, 'editHappyHour'])->name('happy-hour.edit');
            Route::put('/happy-hour/{happyHourDeal}', [AdminPromotionController::class, 'updateHappyHour'])->name('happy-hour.update');
            Route::delete('/happy-hour/{happyHourDeal}', [AdminPromotionController::class, 'destroyHappyHour'])->name('happy-hour.destroy');
            Route::post('/happy-hour/{happyHourDeal}/toggle-status', [AdminPromotionController::class, 'toggleHappyHourStatus'])->name('happy-hour.toggle-status');
        });

        // ---------------------------------------------
        // STOCK MANAGEMENT
        // ---------------------------------------------
        Route::prefix('stock')->name('stock.')->group(function () {
            // Stock Dashboard
            Route::get('dashboard', [StockDashboardController::class, 'index'])->name('dashboard');

            // AJAX API endpoints for dashboard
            Route::get('api/low-stock', [StockDashboardController::class, 'lowStockAlert'])->name('api.low-stock');
            Route::get('api/critical-alert', [StockDashboardController::class, 'criticalAlert'])->name('api.critical-alert');
            Route::get('api/transactions', [StockDashboardController::class, 'getTransactions'])->name('api.transactions');

            // Stock Items Management
            Route::prefix('items')->name('items.')->group(function () {
                Route::get('/', [StockItemController::class, 'index'])->name('index');
                Route::get('create', [StockItemController::class, 'create'])->name('create');
                Route::post('/', [StockItemController::class, 'store'])->name('store');
                Route::get('{item}', [StockItemController::class, 'show'])->name('show');
                Route::get('{item}/edit', [StockItemController::class, 'edit'])->name('edit');
                Route::put('{item}', [StockItemController::class, 'update'])->name('update');
                Route::delete('{item}', [StockItemController::class, 'destroy'])->name('destroy');

                // AJAX endpoints
                Route::post('{item}/toggle-status', [StockItemController::class, 'toggleStatus'])->name('toggle-status');
                Route::post('{item}/adjust-stock', [StockItemController::class, 'adjustStock'])->name('adjust-stock');
            });

            // Suppliers Management
            Route::prefix('suppliers')->name('suppliers.')->group(function () {
                Route::get('/', [SupplierController::class, 'index'])->name('index');
                Route::get('create', [SupplierController::class, 'create'])->name('create');
                Route::post('/', [SupplierController::class, 'store'])->name('store');
                Route::get('{supplier}/edit', [SupplierController::class, 'edit'])->name('edit');
                Route::put('{supplier}', [SupplierController::class, 'update'])->name('update');
                Route::delete('{supplier}', [SupplierController::class, 'destroy'])->name('destroy');

                // AJAX endpoints
                Route::post('{supplier}/toggle-status', [SupplierController::class, 'toggleStatus'])->name('toggle-status');
            });

            // Purchase Orders Management
            Route::prefix('purchase-orders')->name('purchase-orders.')->group(function () {
                Route::get('/', [PurchaseOrderController::class, 'index'])->name('index');
                Route::get('{purchaseOrder}', [PurchaseOrderController::class, 'show'])->name('show');
                Route::post('{purchaseOrder}/approve', [PurchaseOrderController::class, 'approve'])->name('approve');
                Route::post('{purchaseOrder}/mark-received', [PurchaseOrderController::class, 'markAsReceived'])->name('mark-received');
                Route::delete('{purchaseOrder}', [PurchaseOrderController::class, 'destroy'])->name('destroy');
            });
        });
    }); // End of admin routes

}); // End of authenticated routes

// =============================================
// QR CODE PUBLIC ROUTES (No Authentication)
// =============================================
Route::prefix('qr')->name('qr.')->group(function () {

    // Main QR Pages
    Route::get('menu', [QRMenuController::class, 'index'])->name('menu');
    Route::get('guest/menu', [QRMenuController::class, 'guestMenu'])->name('guest.menu');
    Route::get('cart', [QRMenuController::class, 'viewCart'])->name('cart');
    Route::get('error', [QRMenuController::class, 'error'])->name('error');
    Route::get('track', [QRMenuController::class, 'showTrackingPage'])->name('track');

    // Cart Management
    Route::prefix('cart')->name('cart.')->group(function () {
        Route::post('add', [QRMenuController::class, 'addToCart'])->name('add');
        Route::post('update', [QRMenuController::class, 'updateCart'])->name('update');
    });

    // Order Management
    Route::prefix('order')->name('order.')->group(function () {
        Route::post('place', [QRMenuController::class, 'placeOrder'])->name('place');
    });

    // Payment Management
    Route::get('payment', [QRPaymentController::class, 'showPayment'])->name('payment');
    Route::post('payment/process', [QRPaymentController::class, 'processPayment'])->name('payment.process');
    Route::get('payment/confirmation', [QRPaymentController::class, 'showConfirmation'])->name('payment.confirmation');

    // Service Requests
    Route::prefix('waiter')->name('waiter.')->group(function () {
        Route::post('call', [QRMenuController::class, 'callWaiter'])->name('call');
    });

    // API Endpoints
    Route::prefix('api')->name('api.')->group(function () {
        Route::post('track-order', [QRMenuController::class, 'trackOrder'])->name('track-order');
        Route::get('kitchen-status', [QRMenuController::class, 'getKitchenStatus'])->name('kitchen-status');
    });
});

// =============================================
// AUTHENTICATION ROUTES
// =============================================
require __DIR__ . '/auth.php';

// Custom email verification route untuk customer
Route::get('customer/verify-email/{id}/{hash}', function ($id, $hash) {
    $user = \App\Models\User::findOrFail($id);

    if (!hash_equals(sha1($user->getEmailForVerification()), $hash)) {
        return redirect()->route('customer.account.index')->with('error', 'Invalid verification link.');
    }

    if ($user->hasVerifiedEmail()) {
        return redirect()->route('customer.account.index')->with('success', 'Your email is already verified!');
    }

    if ($user->markEmailAsVerified()) {
        event(new \Illuminate\Auth\Events\Verified($user));
    }

    return redirect()->route('customer.account.index')->with('success', 'Email verified successfully!');
})->name('customer.verification.verify');

// Utility route to clear cart
Route::get('/clear-cart', function() {
    return view('clear-cart');
});

require __DIR__.'/test-debug.php';

// Test route for 404 page
Route::get('/test-404', function () {
    abort(404);
});
