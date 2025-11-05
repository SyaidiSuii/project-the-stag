<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules;

class AccountController extends Controller
{
    /**
     * Display customer account page.
     */
    public function index()
    {
        // Check if user is authenticated
        if (!auth()->check()) {
            // Guest user data
            $data = [
                'user' => null,
                'profile' => null,
                'orderCount' => 0,
                'totalSpent' => 0,
                'favoriteDish' => 'None yet',
                'loyaltyPoints' => '0',
                'membershipLevel' => 'Guest',
                'memberSince' => 'Not registered',
                'isGuest' => true,
            ];
            
            return view('customer.account.index', $data);
        }
        
        try {
            $user = auth()->user()->load(['customerProfile', 'orders']);
            
            // Get or create customer profile
            $profile = $user->customerProfile;
            if (!$profile) {
                $profile = $user->customerProfile()->create([
                    'name' => $user->name,
                    'visit_count' => 0,
                    'total_spent' => 0.00,
                ]);
            }

            // Get user stats with fallbacks (exclude cancelled orders)
            $orderCount = $user->orders()->where('order_status', '!=', 'cancelled')->count() ?? 0;
            $loyaltyPoints = $user->points_balance ?? 0; // PHASE 1.1: Use users.points_balance

            // Get total spent from customer profile (updated via background jobs)
            $totalSpent = $profile->total_spent ?? 0;

            // Find favorite dish - use AI recommendation (with fallback to manual calculation)
            $favoriteDish = 'None yet';
            if ($orderCount > 0) {
                try {
                    // Try AI recommendation first
                    $recommendationService = app(\App\Services\RecommendationService::class);
                    $recommendedItems = $recommendationService->getRecommendations($user->id, 1);

                    if (!empty($recommendedItems)) {
                        $topMenuItem = \App\Models\MenuItem::find($recommendedItems[0]);
                        if ($topMenuItem) {
                            $favoriteDish = $topMenuItem->name;
                        }
                    } else {
                        // Fallback to manual calculation if no AI recommendations
                        $mostOrderedItem = \App\Models\OrderItem::selectRaw('menu_item_id, SUM(quantity) as total_quantity')
                            ->join('orders', 'order_items.order_id', '=', 'orders.id')
                            ->where('orders.user_id', $user->id)
                            ->where('orders.order_status', '!=', 'cancelled')
                            ->groupBy('menu_item_id')
                            ->orderBy('total_quantity', 'desc')
                            ->first();

                        if ($mostOrderedItem && $mostOrderedItem->menuItem) {
                            $favoriteDish = $mostOrderedItem->menuItem->name;
                        }
                    }
                } catch (\Exception $e) {
                    // If AI fails, fallback to manual calculation
                    \Log::warning('AI recommendation failed for favorite dish, using fallback', [
                        'user_id' => $user->id,
                        'error' => $e->getMessage()
                    ]);

                    $mostOrderedItem = \App\Models\OrderItem::selectRaw('menu_item_id, SUM(quantity) as total_quantity')
                        ->join('orders', 'order_items.order_id', '=', 'orders.id')
                        ->where('orders.user_id', $user->id)
                        ->where('orders.order_status', '!=', 'cancelled')
                        ->groupBy('menu_item_id')
                        ->orderBy('total_quantity', 'desc')
                        ->first();

                    if ($mostOrderedItem && $mostOrderedItem->menuItem) {
                        $favoriteDish = $mostOrderedItem->menuItem->name;
                    }
                }
            }

            // FIXED: Use TierService to calculate proper tier (same logic as rewards page)
            $tierService = app(\App\Services\Loyalty\TierService::class);
            $currentTier = $tierService->calculateEligibleTier($user);
            $membershipLevel = $currentTier ? $currentTier->name : 'No Tier Yet';

            // Calculate member since with fallback
            $memberSince = $user->created_at ? $user->created_at->format('F Y') : 'Recently';

            $data = [
                'user' => $user,
                'profile' => $profile,
                'orderCount' => $orderCount,
                'totalSpent' => $totalSpent,
                'favoriteDish' => $favoriteDish,
                'loyaltyPoints' => number_format($loyaltyPoints),
                'membershipLevel' => $membershipLevel,
                'memberSince' => $memberSince,
                'isGuest' => false,
            ];
            
            return view('customer.account.index', $data);
            
        } catch (\Exception $e) {
            // Fallback data if there are issues
            $user = auth()->user();
            $data = [
                'user' => $user,
                'profile' => null,
                'orderCount' => 0,
                'totalSpent' => 0,
                'favoriteDish' => 'None yet',
                'loyaltyPoints' => '0',
                'membershipLevel' => 'Bronze Member',
                'memberSince' => 'Recently',
                'isGuest' => false,
            ];
            
            return view('customer.account.index', $data);
        }
    }
    
    /**
     * Update customer account information.
     */
    public function update(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => 'required|email|unique:users,email,' . auth()->id(),
            'phone_number' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date',
        ]);
        
        $user = auth()->user()->load('customerProfile');
        
        // Update user table
        $user->update([
            'name' => trim($request->first_name . ' ' . $request->last_name),
            'email' => $request->email,
            'phone_number' => $request->phone_number,
        ]);
        
        // Get or create customer profile
        $profile = $user->customerProfile;
        if (!$profile) {
            $profile = $user->customerProfile()->create([
                'name' => $user->name,
                'visit_count' => 0,
                'total_spent' => 0.00,
            ]);
        }
        
        // Update customer profile
        $profile->update([
            'date_of_birth' => $request->date_of_birth,
        ]);
        
        return redirect()->route('customer.account.index')
                        ->with('success', 'Profile updated successfully!');
    }

    /**
     * Change user password.
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required'],
            'new_password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = auth()->user();

        // Check if current password is correct
        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->route('customer.account.index')
                            ->with('error', 'Current password is incorrect!');
        }

        // Update password
        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        return redirect()->route('customer.account.index')
                        ->with('success', 'Password changed successfully!');
    }

    /**
     * Delete user account and all associated data.
     */
    public function deleteAccount(Request $request)
    {
        $request->validate([
            'password' => ['required'],
            'confirm_delete' => ['required', 'in:DELETE'],
        ]);

        $user = auth()->user();

        // Verify password
        if (!Hash::check($request->password, $user->password)) {
            return redirect()->route('customer.account.index')
                            ->with('error', 'Incorrect password. Account deletion cancelled.');
        }

        try {
            // Load relationships before deletion
            $user->load(['orders', 'customerProfile']);
            
            // Delete associated data first
            $user->orders()->delete(); // Soft delete orders
            $user->customerProfile()->delete(); // Soft delete customer profile
            
            // Logout user
            Auth::logout();
            
            // Soft delete user account
            $user->delete();
            
            // Clear session data
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect('/')->with('success', 'Your account has been permanently deleted. Thank you for using The Stag SmartDine.');
            
        } catch (\Exception $e) {
            return redirect()->route('customer.account.index')
                            ->with('error', 'An error occurred while deleting your account. Please try again or contact support.');
        }
    }

    /**
     * Send password reset email.
     */
    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        // Send password reset link
        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return redirect()->route('customer.account.index')
                            ->with('success', 'Password reset link has been sent to your email!');
        } else {
            return redirect()->route('customer.account.index')
                            ->with('error', 'Unable to send password reset link. Please try again.');
        }
    }
}