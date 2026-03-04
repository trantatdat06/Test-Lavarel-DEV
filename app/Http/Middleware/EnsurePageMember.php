<?php

namespace App\Http\Middleware;

use App\Models\Page;
use Closure;
use Illuminate\Http\Request;

/**
 * Ensure the authenticated user is an approved member of the requested page.
 * Usage: Route::middleware('page.member:admin,content_manager')
 */
class EnsurePageMember
{
    public function handle(Request $request, Closure $next, string ...$roles)
    {
        $user = $request->user();
        $page = $request->route('page'); // expects Page model binding

        if (!$user) {
            abort(401, 'Unauthenticated.');
        }

        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        $member = $page->members()
                       ->where('user_id', $user->id)
                       ->where('status', 'approved')
                       ->first();

        if (!$member) {
            abort(403, 'Bạn không phải thành viên của trang này.');
        }

        if (!empty($roles) && !in_array($member->role, $roles)) {
            abort(403, 'Bạn không có quyền thực hiện hành động này.');
        }

        return $next($request);
    }
}