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

        // If user is kitchen staff and trying to access dashboard/home
        if ($user && $user->hasRole('kitchen_staff') && $request->is('dashboard')) {
            // Redirect to KDS instead
            return redirect()->route('kds.index');
        }

        return $next($request);
    }
}
