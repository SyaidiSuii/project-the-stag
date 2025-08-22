<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\ProfileUpdateRequest;
use App\Http\Requests\CustomerProfileUpdateRequest;
use App\Models\CustomerProfile;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();
        $customerProfile = $user->customerProfile ?? new CustomerProfile();
        
        return view('profile.edit', [
            'user' => $user,
            'customerProfile' => $customerProfile,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $user->fill($request->validated());

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Update customer profile information.
     */
    public function updateCustomerProfile(CustomerProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validated();
        
        // Get or create customer profile
        $customerProfile = $user->customerProfile ?? new CustomerProfile(['user_id' => $user->id]);

        // Handle photo upload
        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($customerProfile->photo) {
                Storage::disk('public')->delete($customerProfile->photo);
            }
            
            $photo = $request->file('photo');
            $photoPath = $photo->store('customer_photos', 'public');
            $customerProfile->photo = $photoPath;
        }

        // Fill other data
        $customerProfile->fill($validated);

        // Handle dietary preferences
        if ($request->has('dietary_preferences')) {
            $customerProfile->dietary_preferences = $validated['dietary_preferences'];
        }

        $customerProfile->save();

        return Redirect::route('profile.edit')->with('status', 'customer-profile-updated');
    }

    /**
     * Delete customer profile photo.
     */
    public function deletePhoto(Request $request): RedirectResponse
    {
        $user = $request->user();
        $customerProfile = $user->customerProfile;

        if ($customerProfile && $customerProfile->photo) {
            Storage::disk('public')->delete($customerProfile->photo);
            $customerProfile->photo = null;
            $customerProfile->save();
        }

        return Redirect::route('profile.edit')->with('status', 'photo-deleted');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        // Delete customer profile photo if exists
        if ($user->customerProfile && $user->customerProfile->photo) {
            Storage::disk('public')->delete($user->customerProfile->photo);
        }

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}