<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Guests can only access the Explore tab.
 */
class EnsureGuestExploreOnly
{
    public function handle(Request $request, Closure $next)
    {
        if (!$request->user()) {
            // Allow only explore routes for guests
            if (!$request->routeIs('explore.*') && !$request->routeIs('auth.*')) {
                return redirect()->route('explore.index')
                                 ->with('info', 'Vui lòng đăng nhập để xem nội dung này.');
            }
        }

        return $next($request);
    }
}