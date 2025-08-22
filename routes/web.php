<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RoleController;
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

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {return view('dashboard');})->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::patch('/profile/customer-profile', [ProfileController::class, 'updateCustomerProfile'])->name('profile.customer.update');
    Route::delete('/profile/photo', [ProfileController::class, 'deletePhoto'])->name('profile.photo.delete');
    
    Route::resource('user', UserController::class);
    Route::resource('role', RoleController::class);
    Route::resource('table', TableController::class);

    Route::get('table-reservation/today', [TableReservationController::class, 'todayReservations'])->name('table-reservation.today');
    Route::post('table-reservation/{tableReservation}/update-status', [TableReservationController::class, 'updateStatus'])->name('table-reservation.update-status');
    Route::resource('table-reservation', TableReservationController::class);
    Route::resource('table-layout-config', TableLayoutConfigController::class);

    Route::post('table-layout-config/{tableLayoutConfig}/toggle-status', [TableLayoutConfigController::class, 'toggleStatus'])->name('table-layout-config.toggle-status');
    Route::post('table-layout-config/{tableLayoutConfig}/duplicate', [TableLayoutConfigController::class, 'duplicate'])->name('table-layout-config.duplicate');
    Route::get('api/table-layouts/active', [TableLayoutConfigController::class, 'getActiveLayouts'])->name('api.table-layouts.active');
    Route::get('api/table-layouts/{tableLayoutConfig}', [TableLayoutConfigController::class, 'getLayoutDetails'])->name('api.table-layouts.details');
    Route::get('api/table-layouts/statistics', [TableLayoutConfigController::class, 'getStatistics'])->name('api.table-layouts.statistics');
    Route::resource('table-layout-config', TableLayoutConfigController::class);

    // Menu Items routes - IMPORTANT: Specific routes MUST come BEFORE apiResource

    // Route to display the menu items page (view)
    Route::get('menu-items/featured', [MenuItemController::class, 'getFeatured'])->name('menu-items.featured');
    Route::get('menu-items/stats', [MenuItemController::class, 'getStatistics'])->name('menu-items.stats');
    Route::patch('menu-items/{menuItem}/toggle-availability', [MenuItemController::class, 'toggleAvailability'])->name('menu-items.toggle-availability');
    Route::patch('menu-items/{menuItem}/toggle-featured', [MenuItemController::class, 'toggleFeatured'])->name('menu-items.toggle-featured');
    Route::patch('menu-items/{menuItem}/rating', [MenuItemController::class, 'updateRating'])->name('menu-items.rating');
    
    // Basic CRUD routes - MUST come AFTER specific routes
    Route::resource('menu-items', MenuItemController::class);

    // Order routes - IMPORTANT: Specific routes MUST come BEFORE resource routes
    Route::get('order/today', [OrderController::class, 'today'])->name('order.today');
    Route::post('order/{order}/update-status', [OrderController::class, 'updateStatus'])->name('order.updateStatus');
    Route::post('order/{order}/update-payment-status', [OrderController::class, 'updatePaymentStatus'])->name('order.updatePaymentStatus');
    Route::get('order/by-status', [OrderController::class, 'getByStatus'])->name('order.getByStatus');
    Route::post('order/{order}/cancel', [OrderController::class, 'cancel'])->name('order.cancel');
    Route::get('order/{order}/duplicate', [OrderController::class, 'duplicate'])->name('order.duplicate');
    
    // Basic CRUD routes for orders - MUST come AFTER specific routes
    Route::resource('order', OrderController::class);

    // Order Item routes - IMPORTANT: Specific routes MUST come BEFORE resource routes
    Route::get('order-item/kitchen', [OrderItemController::class, 'kitchen'])->name('order-item.kitchen');
    Route::post('order-item/{orderItem}/update-status', [OrderItemController::class, 'updateStatus'])->name('order-item.updateStatus');
    Route::get('order-item/by-order', [OrderItemController::class, 'getByOrder'])->name('order-item.getByOrder');
    Route::get('order-item/by-status', [OrderItemController::class, 'getByStatus'])->name('order-item.getByStatus');
    Route::post('order-item/bulk-update-status', [OrderItemController::class, 'bulkUpdateStatus'])->name('order-item.bulkUpdateStatus');
    Route::get('order-item/calculate-total', [OrderItemController::class, 'calculateTotal'])->name('order-item.calculateTotal');
    Route::get('order-item/get-menu-item-price', [OrderItemController::class, 'getMenuItemPrice'])->name('order-item.getMenuItemPrice');
    Route::get('order-item/{orderItem}/duplicate', [OrderItemController::class, 'duplicate'])->name('order-item.duplicate');
    
    // Basic CRUD routes for order items - MUST come AFTER specific routes
    Route::resource('order-item', OrderItemController::class);

    // Order ETA routes - IMPORTANT: Specific routes MUST come BEFORE resource routes
    Route::post('order-etas/{orderEta}/update-estimate', [OrderEtasController::class, 'updateEstimate'])->name('order-etas.updateEstimate');
    Route::post('order-etas/{orderEta}/mark-completed', [OrderEtasController::class, 'markCompleted'])->name('order-etas.markCompleted');
    Route::post('order-etas/{orderEta}/notify-customer', [OrderEtasController::class, 'notifyCustomer'])->name('order-etas.notifyCustomer');
    Route::get('order-etas/delayed-orders', [OrderEtasController::class, 'getDelayedOrders'])->name('order-etas.getDelayedOrders');
    Route::get('order-etas/statistics', [OrderEtasController::class, 'getStatistics'])->name('order-etas.getStatistics');
    Route::get('order-etas/needing-attention', [OrderEtasController::class, 'getNeedingAttention'])->name('order-etas.getNeedingAttention');
    
    // Basic CRUD routes for order ETAs - MUST come AFTER specific routes
    Route::resource('order-etas', OrderEtasController::class);
    // Di routes/api.php
    Route::get('order-trackings/stats/performance', [OrderTrackingController::class, 'getPerformanceStats'])->name('order-trackings.stats.performance');
    Route::get('order-trackings/stations/active-orders', [OrderTrackingController::class, 'getActiveOrdersByStation'])->name('order-trackings.stations.active-orders');
    Route::get('orders/{id}/tracking-history', [OrderTrackingController::class, 'getOrderHistory'])->name('orders.tracking-history');
    Route::patch('order-trackings/{orderTracking}/status', [OrderTrackingController::class, 'updateStatus'])->name('order-trackings.update-status');
    
    // Basic CRUD routes for order trackings - MUST come AFTER specific routes
    Route::resource('order-trackings', OrderTrackingController::class);

    // Sale Analytics routes - IMPORTANT: Specific routes MUST come BEFORE resource routes
    Route::get('sale-analytics/dashboard-stats', [SaleAnalyticsController::class, 'getDashboardStats'])->name('sale-analytics.dashboard-stats');
    Route::get('sale-analytics/date-range', [SaleAnalyticsController::class, 'getDateRangeAnalytics'])->name('sale-analytics.date-range');
    Route::post('sale-analytics/generate/{date?}', [SaleAnalyticsController::class, 'generateDailyAnalytics'])->name('sale-analytics.generate');
    Route::get('sale-analytics/popular-items', [SaleAnalyticsController::class, 'getPopularItems'])->name('sale-analytics.popular-items');
    Route::get('sale-analytics/peak-hours', [SaleAnalyticsController::class, 'getPeakHours'])->name('sale-analytics.peak-hours');
    Route::get('sale-analytics/customer-analytics', [SaleAnalyticsController::class, 'getCustomerAnalytics'])->name('sale-analytics.customer-analytics');
    Route::get('sale-analytics/trends', [SaleAnalyticsController::class, 'getTrends'])->name('sale-analytics.trends');

    // Basic CRUD routes for sale analytics - MUST come AFTER specific routes
    Route::resource('sale-analytics', SaleAnalyticsController::class);

});

require __DIR__.'/auth.php';