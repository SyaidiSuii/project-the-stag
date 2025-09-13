<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Providers\RouteServiceProvider;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class NewPasswordController extends Controller
{
    /**
     * Display the password reset view.
     */
    public function create(Request $request): View
    {
        return view('auth.reset-password', ['request' => $request]);
    }

    /**
     * Handle an incoming new password request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Here we will attempt to reset the user's password. If it is successful we
        // will update the password on an actual user model and persist it to the
        // database. Otherwise we will parse the error and return the response.
        $user = null;
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($resetUser) use ($request, &$user) {
                $user = $resetUser;
                $user->forceFill([
                    'password' => Hash::make($request->password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        // If the password was successfully reset, login user and redirect to customer dashboard
        if ($status == Password::PASSWORD_RESET) {
            Auth::login($user);
            
            // Debug - Check current authentication state
            if (Auth::check()) {
                \Log::info('Password reset successful - User logged in: ' . Auth::user()->email);
            } else {
                \Log::error('Password reset - Login failed');
            }
            
            // Force redirect to account page and override any default behavior
            return redirect('/customer/account')
                           ->with('success', 'Password has been reset successfully!')
                           ->withCookies([
                               cookie('password_reset_success', 'true', 1)
                           ]);
        } else {
            \Log::error('Password reset failed: ' . $status);
            return back()->withInput($request->only('email'))
                        ->withErrors(['email' => __($status)]);
        }
    }
}
