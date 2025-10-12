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
use App\Http\Controllers\Admin\RewardsController;
use App\Http\Controllers\QR\MenuController as QRMenuController;
use App\Http\Controllers\QR\PaymentController as QRPaymentController;
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
use App\Http\Controllers\Customer\BookingPaymentController as CustomerBookingPaymentController;

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

    // Unified Menu Page
    Route::get('/menu', [\App\Http\Controllers\Customer\MenuController::class, 'index'])->name('menu.index');
    Route::get('/menu/data', [\App\Http\Controllers\Customer\MenuController::class, 'getMenuData'])->name('menu.data');

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
    Route::get('/booking', [CustomerBookingController::class, 'index'])->name('booking.index');
    Route::post('/booking/store', [CustomerBookingController::class, 'store'])->name('booking.store');
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
        Route::post('/merge', [CustomerCartController::class, 'mergeCart'])->name('merge');
    });
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
        // REWARDS MANAGEMENT
        // ---------------------------------------------
        // REWARDS MANAGEMENT
        // ---------------------------------------------
        Route::prefix('rewards')->name('rewards.')->group(function () {
            // Main rewards page - redirect to rewards list
            Route::get('/', [RewardsController::class, 'rewardsIndex'])->name('index');

            // Create/Edit Forms (Old - for backward compatibility)
            Route::get('create', [RewardsController::class, 'create'])->name('create');
            Route::get('{reward}/edit', [RewardsController::class, 'edit'])->name('edit');

            // === NEW SECTION-BASED ROUTES ===
            // Rewards Section
            Route::prefix('rewards')->name('rewards.')->group(function () {
                Route::get('/', [RewardsController::class, 'rewardsIndex'])->name('index');
                Route::get('/create', [RewardsController::class, 'rewardsCreate'])->name('create');
                Route::post('/', [RewardsController::class, 'rewardsStore'])->name('store');
                Route::get('/{reward}/edit', [RewardsController::class, 'rewardsEdit'])->name('edit');
                Route::put('/{reward}', [RewardsController::class, 'rewardsUpdate'])->name('update');
                Route::delete('/{reward}', [RewardsController::class, 'rewardsDestroy'])->name('destroy');
            });

            // Voucher Templates Section
            Route::prefix('voucher-templates')->name('voucher-templates.')->group(function () {
                Route::get('/', [RewardsController::class, 'voucherTemplatesIndex'])->name('index');
                Route::get('/create', [RewardsController::class, 'voucherTemplatesCreate'])->name('create');
                Route::post('/', [RewardsController::class, 'voucherTemplatesStore'])->name('store');
                Route::get('/{template}/edit', [RewardsController::class, 'voucherTemplatesEdit'])->name('edit');
                Route::put('/{template}', [RewardsController::class, 'voucherTemplatesUpdate'])->name('update');
                Route::delete('/{template}', [RewardsController::class, 'voucherTemplatesDestroy'])->name('destroy');
            });

            // Achievements Section
            Route::prefix('achievements')->name('achievements.')->group(function () {
                Route::get('/', [RewardsController::class, 'achievementsIndex'])->name('index');
                Route::get('/create', [RewardsController::class, 'achievementsCreate'])->name('create');
                Route::post('/', [RewardsController::class, 'achievementsStore'])->name('store');
                Route::get('/{achievement}/edit', [RewardsController::class, 'achievementsEdit'])->name('edit');
                Route::put('/{achievement}', [RewardsController::class, 'achievementsUpdate'])->name('update');
                Route::delete('/{achievement}', [RewardsController::class, 'achievementsDestroy'])->name('destroy');
            });

            // Voucher Collections Section
            Route::prefix('voucher-collections')->name('voucher-collections.')->group(function () {
                Route::get('/', [RewardsController::class, 'voucherCollectionsIndex'])->name('index');
                Route::get('/create', [RewardsController::class, 'voucherCollectionsCreate'])->name('create');
                Route::post('/', [RewardsController::class, 'voucherCollectionsStore'])->name('store');
                Route::get('/{collection}/edit', [RewardsController::class, 'voucherCollectionsEdit'])->name('edit');
                Route::put('/{collection}', [RewardsController::class, 'voucherCollectionsUpdate'])->name('update');
                Route::delete('/{collection}', [RewardsController::class, 'voucherCollectionsDestroy'])->name('destroy');
            });

            // Bonus Challenges Section
            Route::prefix('bonus-challenges')->name('bonus-challenges.')->group(function () {
                Route::get('/', [RewardsController::class, 'bonusChallengesIndex'])->name('index');
                Route::get('/create', [RewardsController::class, 'bonusChallengesCreate'])->name('create');
                Route::post('/', [RewardsController::class, 'bonusChallengesStore'])->name('store');
                Route::get('/{challenge}/edit', [RewardsController::class, 'bonusChallengesEdit'])->name('edit');
                Route::put('/{challenge}', [RewardsController::class, 'bonusChallengesUpdate'])->name('update');
                Route::delete('/{challenge}', [RewardsController::class, 'bonusChallengesDestroy'])->name('destroy');
            });

            // Special Events Section
            Route::prefix('special-events')->name('special-events.')->group(function () {
                Route::get('/', [RewardsController::class, 'specialEventsIndex'])->name('index');
                Route::get('/create', [RewardsController::class, 'specialEventsCreate'])->name('create');
                Route::post('/', [RewardsController::class, 'specialEventsStore'])->name('store');
                Route::get('/{event}/edit', [RewardsController::class, 'specialEventsEdit'])->name('edit');
                Route::put('/{event}', [RewardsController::class, 'specialEventsUpdate'])->name('update');
                Route::delete('/{event}', [RewardsController::class, 'specialEventsDestroy'])->name('destroy');
            });

            // Loyalty Tiers Section
            Route::prefix('loyalty-tiers')->name('loyalty-tiers.')->group(function () {
                Route::get('/', [RewardsController::class, 'loyaltyTiersIndex'])->name('index');
                Route::get('/create', [RewardsController::class, 'loyaltyTiersCreate'])->name('create');
                Route::post('/', [RewardsController::class, 'loyaltyTiersStore'])->name('store');
                Route::get('/{tier}/edit', [RewardsController::class, 'loyaltyTiersEdit'])->name('edit');
                Route::put('/{tier}', [RewardsController::class, 'loyaltyTiersUpdate'])->name('update');
                Route::delete('/{tier}', [RewardsController::class, 'loyaltyTiersDestroy'])->name('destroy');
            });

            // === OLD API ROUTES (for backward compatibility) ===
            // Exchange Points (Rewards) Management
            Route::post('store', [RewardsController::class, 'storeReward'])->name('store');
            Route::put('{reward}', [RewardsController::class, 'updateReward'])->name('update');
            Route::delete('{reward}', [RewardsController::class, 'destroyReward'])->name('destroy');
            Route::patch('{reward}/toggle', [RewardsController::class, 'toggleReward'])->name('toggle');

            // Achievements Management
            Route::post('achievements', [RewardsController::class, 'storeAchievement'])->name('achievements.store');
            Route::put('achievements/{achievement}', [RewardsController::class, 'updateAchievement'])->name('achievements.update');
            Route::delete('achievements/{achievement}', [RewardsController::class, 'destroyAchievement'])->name('achievements.destroy');

            // Bonus Point Challenges Management
            Route::post('bonus-challenges', [RewardsController::class, 'storeBonusPointChallenge'])->name('bonus-challenges.store');
            Route::put('bonus-challenges/{bonusPointChallenge}', [RewardsController::class, 'updateBonusPointChallenge'])->name('bonus-challenges.update');
            Route::delete('bonus-challenges/{bonusPointChallenge}', [RewardsController::class, 'destroyBonusPointChallenge'])->name('bonus-challenges.destroy');

            // Voucher Collections Management
            Route::post('vouchers', [RewardsController::class, 'storeVoucherCollection'])->name('vouchers.store');
            Route::put('vouchers/{voucherCollection}', [RewardsController::class, 'updateVoucherCollection'])->name('vouchers.update');
            Route::delete('vouchers/{voucherCollection}', [RewardsController::class, 'destroyVoucherCollection'])->name('vouchers.destroy');

            // Check-in Settings
            Route::get('checkin-settings', [RewardsController::class, 'checkinIndex'])->name('checkin.index');
            Route::post('checkin-settings', [RewardsController::class, 'updateCheckinSettings'])->name('checkin.update');

            // Redemptions Management
            Route::get('redemptions', [RewardsController::class, 'redemptionsIndex'])->name('redemptions.index');
            Route::post('redemptions/{redemption}/mark-redeemed', [RewardsController::class, 'markRedemptionAsRedeemed'])->name('redemptions.mark-redeemed');

            // Members Management
            Route::get('members', [RewardsController::class, 'membersIndex'])->name('members.index');

            // Settings Management
            Route::post('content', [RewardsController::class, 'updateRewardsContent'])->name('content.update');

            // Voucher generation from templates (keeping this for backward compatibility)
            Route::post('voucher-templates/{voucherTemplate}/generate', [RewardsController::class, 'generateVouchersFromTemplate'])->name('templates.generate');
            Route::patch('special-events/{event}/toggle', [RewardsController::class, 'toggleSpecialEvent'])->name('events.toggle');
        });
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
