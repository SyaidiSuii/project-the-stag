<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\TableController;
use App\Http\Controllers\Admin\TableReservationController;
use App\Http\Controllers\Admin\TableLayoutConfigController;
use App\Http\Controllers\Admin\MenuItemController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\OrderItemController;
use App\Http\Controllers\Admin\OrderEtasController; 
use App\Http\Controllers\Admin\OrderTrackingController;
use App\Http\Controllers\Admin\SaleAnalyticsController;
use App\Http\Controllers\Admin\PushNotificationController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\RoleManagementController;
use App\Http\Controllers\Admin\TableSessionController;
use App\Http\Controllers\QR\MenuController as QRMenuController;
use App\Http\Controllers\Admin\MenuCustomizationController;
use App\Http\Controllers\Admin\QuickReorderController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Customer\HomeController as CustomerHomeController;
use App\Http\Controllers\Customer\FoodController as CustomerFoodController;
use App\Http\Controllers\Customer\DrinksController as CustomerDrinksController;
use App\Http\Controllers\Customer\OrdersController as CustomerOrdersController;
use App\Http\Controllers\Customer\RewardsController as CustomerRewardsController;
use App\Http\Controllers\Customer\BookingController as CustomerBookingController;
use App\Http\Controllers\Customer\AccountController as CustomerAccountController;
use App\Http\Controllers\Customer\PaymentController as CustomerPaymentController;
use App\Http\Controllers\Customer\CartController as CustomerCartController;

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


Route::prefix('customer')->name('customer.')->group(function () {
    Route::post('/feedback', [CustomerHomeController::class, 'storeFeedback'])->name('feedback.store');
    Route::get('/food', [CustomerFoodController::class, 'index'])->name('food.index');
    Route::get('/food/data', [CustomerFoodController::class, 'getMenuData'])->name('food.data');
    Route::get('/drinks', [CustomerDrinksController::class, 'index'])->name('drinks.index');
    Route::get('/orders', [CustomerOrdersController::class, 'index'])->name('orders.index');
    Route::get('/rewards', [CustomerRewardsController::class, 'index'])->name('rewards.index');
    Route::get('/booking', [CustomerBookingController::class, 'index'])->name('booking.index');
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
        Route::post('/merge', [CustomerCartController::class, 'mergeCart'])->name('merge');
    });
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
    // ADMIN PANEL SECTION (Admin & Manager Only)
    // =============================================
    Route::prefix('admin')->name('admin.')->middleware(['role:admin|manager'])->group(function () {

        // Admin User Management (Full Access)
        Route::resource('user', UserController::class);
        
        // ---------------------------------------------
        // PERMISSIONS & ROLES MANAGEMENT
        // ---------------------------------------------
        Route::prefix('permissions')->name('permissions.')->group(function () {
            Route::get('{permission}/assign', [PermissionController::class, 'assignForm'])->name('assign.form');
            Route::post('{permission}/assign', [PermissionController::class, 'assignToRoles'])->name('assign');
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
        // TABLE MANAGEMENT
        // ---------------------------------------------
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
        });
        Route::resource('table-layout-config', TableLayoutConfigController::class);

        // Table Sessions
        Route::prefix('table-sessions')->name('table-sessions.')->group(function () {
            Route::get('active', [TableSessionController::class, 'active'])->name('active');
            Route::post('{tableSession}/complete', [TableSessionController::class, 'complete'])->name('complete');
            Route::post('{tableSession}/extend', [TableSessionController::class, 'extend'])->name('extend');
            Route::post('{tableSession}/regenerate-qr', [TableSessionController::class, 'regenerateQR'])->name('regenerate-qr');
            Route::get('{tableSession}/qr-code', [TableSessionController::class, 'qrCode'])->name('qr-code');
            Route::get('{tableSession}/qr-download/{format}', [TableSessionController::class, 'downloadQR'])->name('qr-download')->where('format', 'png|svg');
            Route::get('{tableSession}/qr-preview/{format?}', [TableSessionController::class, 'previewQR'])->name('qr-preview');
            Route::post('expire-old', [TableSessionController::class, 'expireOldSessions'])->name('expire-old');
        });
        Route::resource('table-sessions', TableSessionController::class);

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
        });
        Route::resource('menu-items', MenuItemController::class);

        // Menu Customizations
        Route::prefix('menu-customizations')->name('menu-customizations.')->group(function () {
            Route::get('statistics', [MenuCustomizationController::class, 'getStatistics'])->name('getStatistics');
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
            Route::post('{order}/update-status', [OrderController::class, 'updateStatus'])->name('updateStatus');
            Route::post('{order}/update-payment-status', [OrderController::class, 'updatePaymentStatus'])->name('updatePaymentStatus');
            Route::get('by-status', [OrderController::class, 'getByStatus'])->name('getByStatus');
            Route::post('{order}/cancel', [OrderController::class, 'cancel'])->name('cancel');
            Route::get('{order}/duplicate', [OrderController::class, 'duplicate'])->name('duplicate');
        });
        Route::resource('order', OrderController::class);

        // Order Items
        Route::prefix('order-item')->name('order-item.')->group(function () {
            Route::get('kitchen', [OrderItemController::class, 'kitchen'])->name('kitchen');
            Route::post('{orderItem}/update-status', [OrderItemController::class, 'updateStatus'])->name('updateStatus');
            Route::get('by-order', [OrderItemController::class, 'getByOrder'])->name('getByOrder');
            Route::get('by-status', [OrderItemController::class, 'getByStatus'])->name('getByStatus');
            Route::post('bulk-update-status', [OrderItemController::class, 'bulkUpdateStatus'])->name('bulkUpdateStatus');
            Route::get('calculate-total', [OrderItemController::class, 'calculateTotal'])->name('calculateTotal');
            Route::get('get-menu-item-price', [OrderItemController::class, 'getMenuItemPrice'])->name('getMenuItemPrice');
            Route::get('{orderItem}/duplicate', [OrderItemController::class, 'duplicate'])->name('duplicate');
        });
        Route::resource('order-item', OrderItemController::class);

        // Order ETAs
        Route::prefix('order-etas')->name('order-etas.')->group(function () {
            Route::post('{orderEta}/update-estimate', [OrderEtasController::class, 'updateEstimate'])->name('updateEstimate');
            Route::post('{orderEta}/mark-completed', [OrderEtasController::class, 'markCompleted'])->name('markCompleted');
            Route::post('{orderEta}/notify-customer', [OrderEtasController::class, 'notifyCustomer'])->name('notifyCustomer');
            Route::get('delayed-orders', [OrderEtasController::class, 'getDelayedOrders'])->name('getDelayedOrders');
            Route::get('statistics', [OrderEtasController::class, 'getStatistics'])->name('getStatistics');
            Route::get('needing-attention', [OrderEtasController::class, 'getNeedingAttention'])->name('getNeedingAttention');
        });
        Route::resource('order-etas', OrderEtasController::class);

        // Order Tracking
        Route::prefix('order-trackings')->name('order-trackings.')->group(function () {
            Route::get('stats/performance', [OrderTrackingController::class, 'getPerformanceStats'])->name('stats.performance');
            Route::get('stations/active-orders', [OrderTrackingController::class, 'getActiveOrdersByStation'])->name('stations.active-orders');
            Route::patch('{orderTracking}/status', [OrderTrackingController::class, 'updateStatus'])->name('update-status');
        });
        Route::get('orders/{id}/tracking-history', [OrderTrackingController::class, 'getOrderHistory'])->name('orders.tracking-history');
        Route::resource('order-trackings', OrderTrackingController::class);

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

    }); // End of admin routes

}); // End of authenticated routes

// =============================================
// QR CODE PUBLIC ROUTES (No Authentication)
// =============================================
Route::prefix('qr')->name('qr.')->group(function () {
    
    // Main QR Pages
    Route::get('menu', [QRMenuController::class, 'index'])->name('menu');
    Route::get('cart', [QRMenuController::class, 'viewCart'])->name('cart');
    Route::get('error', [QRMenuController::class, 'error'])->name('error');
    Route::get('track', [QRMenuController::class, 'trackOrder'])->name('track');
    
    // Cart Management
    Route::prefix('cart')->name('cart.')->group(function () {
        Route::post('add', [QRMenuController::class, 'addToCart'])->name('add');
        Route::post('update', [QRMenuController::class, 'updateCart'])->name('update');
    });
    
    // Order Management
    Route::prefix('order')->name('order.')->group(function () {
        Route::post('place', [QRMenuController::class, 'placeOrder'])->name('place');
    });
    
    // Service Requests
    Route::prefix('waiter')->name('waiter.')->group(function () {
        Route::post('call', [QRMenuController::class, 'callWaiter'])->name('call');
    });
    
    // API Endpoints
    Route::prefix('api')->name('api.')->group(function () {
        Route::post('track-order', [QRMenuController::class, 'trackOrder'])->name('track-order');
    });
});

// =============================================
// AUTHENTICATION ROUTES
// =============================================
require __DIR__.'/auth.php';

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