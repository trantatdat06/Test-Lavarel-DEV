<?php

use App\Http\Middleware\EnsureEduEmail;
use App\Http\Middleware\EnsureGuestExploreOnly;
use App\Http\Middleware\EnsurePageMember;
use App\Http\Middleware\EnsureRolePermission;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'edu.email'    => EnsureEduEmail::class,
            'page.member'  => EnsurePageMember::class,
            'role'         => EnsureRolePermission::class,
            'guest.explore'=> EnsureGuestExploreOnly::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();