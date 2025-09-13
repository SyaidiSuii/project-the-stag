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
                    'loyalty_points' => 0,
                    'visit_count' => 0,
                    'total_spent' => 0.00,
                ]);
            }
            
            // Get user stats with fallbacks
            $orderCount = $user->orders->count() ?? 0;
            $loyaltyPoints = $profile->loyalty_points ?? 0;
            
            // Determine membership level based on points
            $membershipLevel = 'Bronze Member';
            if ($loyaltyPoints >= 2000) {
                $membershipLevel = 'Gold Member';
            } elseif ($loyaltyPoints >= 1000) {
                $membershipLevel = 'Silver Member';
            }
            
            // Calculate member since with fallback
            $memberSince = $user->created_at ? $user->created_at->format('F Y') : 'Recently';
            
            $data = [
                'user' => $user,
                'profile' => $profile,
                'orderCount' => $orderCount,
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
                'loyalty_points' => 0,
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