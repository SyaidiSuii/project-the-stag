<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use App\Http\Controllers\RecommendationController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Test endpoint
Route::get('/test', function () {
    return response()->json([
        'message' => 'Hello from Laravel API!',
        'status' => true
    ]);
});

// Authentication Routes
Route::prefix('auth')->group(function () {
    
    // Register
    Route::post('/register', function (Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone_number' => 'nullable|string|max:20',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone_number' => $request->phone_number,
        ]);

        $user->assignRole('customer');
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => 'User registered successfully',
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer'
        ], 201);
    });

    // Login
    Route::post('/login', function (Request $request) {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $user = User::where('email', $request->email)->first();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => 'Login successful',
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer'
        ]);
    });

    // Logout (requires authentication)
    Route::post('/logout', function (Request $request) {
        $request->user()->currentAccessToken()->delete();
        
        return response()->json([
            'status' => true,
            'message' => 'Logged out successfully'
        ]);
    })->middleware('auth:sanctum');

    // Forgot Password
    Route::post('/forgot-password', function (Request $request) {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return response()->json([
                'status' => true,
                'message' => 'Password reset link sent to your email'
            ]);
        }

        throw ValidationException::withMessages([
            'email' => [__($status)],
        ]);
    });

    // Reset Password
    Route::post('/reset-password', function (Request $request) {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return response()->json([
                'status' => true,
                'message' => 'Password has been reset successfully'
            ]);
        }

        throw ValidationException::withMessages([
            'email' => [__($status)],
        ]);
    });

    // Confirm Password (requires authentication)
    Route::post('/confirm-password', function (Request $request) {
        $request->validate([
            'password' => 'required'
        ]);

        if (!Hash::check($request->password, $request->user()->password)) {
            throw ValidationException::withMessages([
                'password' => ['The provided password is incorrect.'],
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Password confirmed successfully'
        ]);
    })->middleware('auth:sanctum');

    // Email Verification Notice
    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();

        return response()->json([
            'status' => true,
            'message' => 'Verification link sent to your email'
        ]);
    })->middleware(['auth:sanctum', 'throttle:6,1']);

    // Verify Email
    Route::post('/email/verify/{id}/{hash}', function (Request $request, $id, $hash) {
        $user = User::findOrFail($id);

        if (!hash_equals(sha1($user->getEmailForVerification()), $hash)) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid verification link'
            ], 400);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'status' => true,
                'message' => 'Email already verified'
            ]);
        }

        if ($user->markEmailAsVerified()) {
            event(new \Illuminate\Auth\Events\Verified($user));
        }

        return response()->json([
            'status' => true,
            'message' => 'Email verified successfully'
        ]);
    })->name('verification.verify');
});

// Protected routes (requires authentication)
Route::middleware('auth:sanctum')->group(function () {
    // Get current user
    Route::get('/user', function (Request $request) {
        return response()->json([
            'status' => true,
            'user' => $request->user()
        ]);
    });

    // Update user profile
    Route::put('/user/profile', function (Request $request) {
        $user = $request->user();
        
        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . $user->id,
            'phone_number' => 'nullable|string|max:20',
        ]);

        $user->update($request->only('name', 'email', 'phone_number'));

        return response()->json([
            'status' => true,
            'message' => 'Profile updated successfully',
            'user' => $user->fresh()
        ]);
    });

    // Change password
    Route::put('/user/password', function (Request $request) {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        if (!Hash::check($request->current_password, $request->user()->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['The provided password is incorrect.'],
            ]);
        }

        $request->user()->update([
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Password updated successfully'
        ]);
    });
});

// AI Recommendation Routes
Route::prefix('recommendations')->group(function () {
    // Health check
    Route::get('/health', [RecommendationController::class, 'health']);
    
    // Public routes
    Route::get('/user/{user_id}', [RecommendationController::class, 'getRecommendations']);
    Route::get('/user/{user_id}/fallback', [RecommendationController::class, 'getRecommendationsWithFallback']);
    
    // Protected routes (require authentication)
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/me', [RecommendationController::class, 'getMyRecommendations']);
        Route::post('/retrain', [RecommendationController::class, 'retrain']);
    });
});

// AI Service Management Routes (Admin only)
Route::prefix('ai-service')->middleware(['auth:sanctum'])->group(function () {
    // Service status and health
    Route::get('/status', [RecommendationController::class, 'getServiceStatus']);
    Route::get('/test-connection', [RecommendationController::class, 'testConnection']);
    
    // Model management (Admin only)
    Route::middleware('role:admin')->group(function () {
        Route::post('/force-retrain', [RecommendationController::class, 'forceRetrain']);
    });
});


