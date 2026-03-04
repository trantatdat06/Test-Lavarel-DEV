<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Only allow @hvnh.edu.vn email addresses to register.
 */
class EnsureEduEmail
{
    public function handle(Request $request, Closure $next)
    {
        $email = $request->input('email', '');

        if (!str_ends_with(strtolower($email), '@hvnh.edu.vn')) {
            return back()->withErrors(['email' => 'Chỉ chấp nhận email @hvnh.edu.vn']);
        }

        return $next($request);
    }
}