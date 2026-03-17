<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;

Route::get('/', function () { return view('index'); });

// Route cho AJAX Tabs
Route::get('/src/{path}', function ($path) {
    $viewName = 'src.' . str_replace(['/', '.blade.php'], ['.', ''], $path);
    return view()->exists($viewName) ? view($viewName) : abort(404);
})->where('path', '.*');

// Route Auth
Route::get('/auth', function () { return view('page.auth.auth'); });

// PROFILE ROUTES
Route::get('/profile/{studentCode}', [ProfileController::class, 'show']);
Route::get('/profile/{studentCode}/tab/{tabName}', [ProfileController::class, 'getTab']);
Route::post('/profile/{studentCode}/request-page', [ProfileController::class, 'submitPageRequest']);

// ADMIN ROUTES (Tạm bỏ middleware để test)
Route::prefix('admin')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('admin.index');
    Route::post('/page/{id}/approve', [AdminController::class, 'approvePage'])->name('admin.page.approve');
    Route::post('/page/{id}/reject', [AdminController::class, 'rejectPage'])->name('admin.page.reject');
});