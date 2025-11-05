<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectKitchenStaff
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Only process if user is kitchen staff
        if (!$user || !$user->hasRole('kitchen_staff')) {
            return $next($request);
        }

        // Allow access to logout route
        if ($request->is('logout')) {
            return $next($request);
        }

        // Allowed kitchen routes for kitchen staff
        $allowedRoutes = [
            'admin/kitchen/kds*',
            'admin/kitchen/orders*',
            'admin/order/*/update-status',
            'profile*',
        ];

        // Check if current route is allowed
        foreach ($allowedRoutes as $pattern) {
            if ($request->is($pattern)) {
                // If accessing KDS, ensure they can only see their assigned station
                if ($request->is('admin/kitchen/kds')) {
                    $stationId = $request->query('station_id');
                    $assignedStationId = $user->assigned_station_id;

                    // If they have an assigned station and trying to access different station, redirect
                    if ($assignedStationId && $stationId && $stationId != $assignedStationId) {
                        return redirect()->route('admin.kitchen.kds', ['station_id' => $assignedStationId])
                            ->with('error', 'You can only access your assigned station.');
                    }

                    // If they have assigned station but no station_id in URL, redirect with their station
                    if ($assignedStationId && !$stationId) {
                        return redirect()->route('admin.kitchen.kds', ['station_id' => $assignedStationId]);
                    }
                }

                return $next($request);
            }
        }

        // Block access to all other routes (admin pages, customer pages, etc.)
        // Redirect to their KDS page
        $redirectUrl = $user->assigned_station_id
            ? route('admin.kitchen.kds', ['station_id' => $user->assigned_station_id])
            : route('admin.kitchen.kds');

        return redirect($redirectUrl)
            ->with('error', 'Access denied. Kitchen staff can only access the Kitchen Display System.');
    }
}
