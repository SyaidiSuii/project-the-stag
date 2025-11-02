<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(Request $request): View
    {
        // Store redirect URL in session if provided
        if ($request->has('redirect')) {
            session(['url.intended' => $request->get('redirect')]);
        }

        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // Auto-promote first admin/manager to super admin if none exists
        $user = auth()->user();
        if ($user && $user->hasAnyRole(['admin', 'manager'])) {
            // Check if any user has super-admin role
            $superAdminRole = Role::where('name', 'super-admin')->first();
            $hasSuperAdmin = $superAdminRole && $superAdminRole->users()->exists();

            if (!$hasSuperAdmin) {
                $user->assignRole('super-admin');
                session()->flash('success', 'You have been automatically promoted to Super Admin as the first admin user!');
            }
        }

        // Redirect kitchen staff to their assigned station KDS
        if ($user && $user->hasRole('kitchen_staff')) {
            if ($user->assigned_station_id) {
                return redirect()->route('admin.kitchen.kds', ['station_id' => $user->assigned_station_id]);
            }
            // If no station assigned, redirect to KDS index (will show all orders)
            return redirect()->route('admin.kitchen.kds');
        }

        return redirect()->intended(RouteServiceProvider::HOME);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        // Use cookie to pass message across session invalidation
        return redirect('/')
            ->withCookie(cookie('logout_message', 'You have been successfully logged out.', 1));
    }
}
