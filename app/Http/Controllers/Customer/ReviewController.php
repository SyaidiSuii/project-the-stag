<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\MenuItemReview;
use App\Models\Order;
use App\Models\MenuItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReviewController extends Controller
{
    /**
     * Show review form for a specific order
     */
    public function create($orderId)
    {
        $userId = Auth::id();

        // Get the order with items
        $order = Order::with(['items.menuItem'])
            ->where('id', $orderId)
            ->where('user_id', $userId)
            ->whereIn('order_status', ['completed', 'served'])
            ->first();

        if (!$order) {
            return redirect()
                ->route('customer.orders.index')
                ->with('error', 'Order not found or not eligible for review.');
        }

        // Get items that haven't been reviewed yet
        $reviewableItems = [];
        foreach ($order->items as $orderItem) {
            if (!$orderItem->menuItem) {
                continue; // Skip if menu item no longer exists
            }

            // Check if already reviewed
            $existingReview = MenuItemReview::where('user_id', $userId)
                ->where('menu_item_id', $orderItem->menu_item_id)
                ->where('order_id', $order->id)
                ->first();

            if (!$existingReview) {
                $reviewableItems[] = [
                    'order_item_id' => $orderItem->id,
                    'menu_item_id' => $orderItem->menu_item_id,
                    'menu_item' => $orderItem->menuItem,
                    'quantity' => $orderItem->quantity
                ];
            }
        }

        // If no items to review, redirect back
        if (empty($reviewableItems)) {
            return redirect()
                ->route('customer.orders.index')
                ->with('info', 'All items from this order have already been reviewed.');
        }

        return view('customer.reviews.create', compact('order', 'reviewableItems'));
    }

    /**
     * Store a new review
     */
    public function store(Request $request)
    {
        $userId = Auth::id();

        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'menu_item_id' => 'required|exists:menu_items,id',
            'rating' => 'required|integer|min:1|max:5',
            'review_text' => 'nullable|string|max:1000',
            'is_anonymous' => 'nullable|boolean',
        ]);

        try {
            DB::beginTransaction();

            // Verify order belongs to user and is completed
            $order = Order::where('id', $request->order_id)
                ->where('user_id', $userId)
                ->whereIn('order_status', ['completed', 'served'])
                ->first();

            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order not found or not eligible for review.'
                ], 404);
            }

            // Verify menu item was in the order
            $orderItem = $order->items()
                ->where('menu_item_id', $request->menu_item_id)
                ->first();

            if (!$orderItem) {
                return response()->json([
                    'success' => false,
                    'message' => 'This item was not part of your order.'
                ], 400);
            }

            // Check if already reviewed
            $existingReview = MenuItemReview::where('user_id', $userId)
                ->where('menu_item_id', $request->menu_item_id)
                ->where('order_id', $request->order_id)
                ->first();

            if ($existingReview) {
                return response()->json([
                    'success' => false,
                    'message' => 'You have already reviewed this item from this order.'
                ], 400);
            }

            // Create the review
            $review = MenuItemReview::create([
                'user_id' => $userId,
                'menu_item_id' => $request->menu_item_id,
                'order_id' => $request->order_id,
                'rating' => $request->rating,
                'review_text' => $request->review_text,
                'is_anonymous' => $request->is_anonymous ?? false,
            ]);

            // Rating will be automatically recalculated via model boot method

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Thank you for your review!',
                'review' => [
                    'id' => $review->id,
                    'rating' => $review->rating,
                    'review_text' => $review->review_text,
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            \Log::error('Failed to create review', [
                'user_id' => $userId,
                'order_id' => $request->order_id,
                'menu_item_id' => $request->menu_item_id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to submit review. Please try again.',
                'debug' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Batch store reviews for multiple items from same order
     */
    public function storeBatch(Request $request)
    {
        $userId = Auth::id();

        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'reviews' => 'required|array|min:1',
            'reviews.*.menu_item_id' => 'required|exists:menu_items,id',
            'reviews.*.rating' => 'required|integer|min:1|max:5',
            'reviews.*.review_text' => 'nullable|string|max:1000',
            'reviews.*.is_anonymous' => 'nullable|boolean',
        ]);

        try {
            DB::beginTransaction();

            // Verify order belongs to user and is completed
            $order = Order::with('items')
                ->where('id', $request->order_id)
                ->where('user_id', $userId)
                ->whereIn('order_status', ['completed', 'served'])
                ->first();

            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order not found or not eligible for review.'
                ], 404);
            }

            $createdReviews = [];
            $errors = [];

            foreach ($request->reviews as $reviewData) {
                // Verify menu item was in the order
                $orderItem = $order->items()
                    ->where('menu_item_id', $reviewData['menu_item_id'])
                    ->first();

                if (!$orderItem) {
                    $errors[] = "Item ID {$reviewData['menu_item_id']} was not part of your order.";
                    continue;
                }

                // Check if already reviewed
                $existingReview = MenuItemReview::where('user_id', $userId)
                    ->where('menu_item_id', $reviewData['menu_item_id'])
                    ->where('order_id', $request->order_id)
                    ->first();

                if ($existingReview) {
                    $errors[] = "You have already reviewed item ID {$reviewData['menu_item_id']}.";
                    continue;
                }

                // Create the review
                $review = MenuItemReview::create([
                    'user_id' => $userId,
                    'menu_item_id' => $reviewData['menu_item_id'],
                    'order_id' => $request->order_id,
                    'rating' => $reviewData['rating'],
                    'review_text' => $reviewData['review_text'] ?? null,
                    'is_anonymous' => $reviewData['is_anonymous'] ?? false,
                ]);

                $createdReviews[] = $review;
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => count($createdReviews) > 0
                    ? 'Thank you for your reviews!'
                    : 'No reviews were submitted.',
                'created_count' => count($createdReviews),
                'errors' => $errors
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            \Log::error('Failed to create batch reviews', [
                'user_id' => $userId,
                'order_id' => $request->order_id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to submit reviews. Please try again.',
                'debug' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Show customer's own reviews
     */
    public function myReviews()
    {
        $userId = Auth::id();

        $reviews = MenuItemReview::with(['menuItem', 'order'])
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('customer.reviews.my-reviews', compact('reviews'));
    }

    /**
     * Show reviews for a specific menu item (public view)
     */
    public function showMenuItemReviews($menuItemId)
    {
        $menuItem = MenuItem::with(['reviews' => function($query) {
            $query->orderBy('created_at', 'desc');
        }, 'reviews.user'])
            ->findOrFail($menuItemId);

        $reviews = $menuItem->reviews()->paginate(10);

        return view('customer.reviews.menu-item-reviews', compact('menuItem', 'reviews'));
    }

    /**
     * Update existing review
     */
    public function update(Request $request, $reviewId)
    {
        $userId = Auth::id();

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review_text' => 'nullable|string|max:1000',
            'is_anonymous' => 'nullable|boolean',
        ]);

        try {
            $review = MenuItemReview::where('id', $reviewId)
                ->where('user_id', $userId)
                ->firstOrFail();

            $review->update([
                'rating' => $request->rating,
                'review_text' => $request->review_text,
                'is_anonymous' => $request->is_anonymous ?? $review->is_anonymous,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Review updated successfully!',
                'review' => $review
            ]);

        } catch (\Exception $e) {
            \Log::error('Failed to update review', [
                'user_id' => $userId,
                'review_id' => $reviewId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update review. Please try again.'
            ], 500);
        }
    }

    /**
     * Delete a review
     */
    public function destroy($reviewId)
    {
        $userId = Auth::id();

        try {
            $review = MenuItemReview::where('id', $reviewId)
                ->where('user_id', $userId)
                ->firstOrFail();

            $review->delete();

            return response()->json([
                'success' => true,
                'message' => 'Review deleted successfully.'
            ]);

        } catch (\Exception $e) {
            \Log::error('Failed to delete review', [
                'user_id' => $userId,
                'review_id' => $reviewId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete review. Please try again.'
            ], 500);
        }
    }
}
