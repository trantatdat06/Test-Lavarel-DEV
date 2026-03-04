<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Ensure authenticated user has one of the given roles.
 * Usage: Route::middleware('role:super_admin,page_admin')
 */
class EnsureRolePermission
{
    public function handle(Request $request, Closure $next, string ...$roles)
    {
        $user = $request->user();

        if (!$user) {
            abort(401);
        }

        if (!in_array($user->role, $roles)) {
            abort(403, 'Bạn không có quyền truy cập.');
        }

        return $next($request);
    }
}