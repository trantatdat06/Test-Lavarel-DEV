<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;

Route::get('/', function () {
    return view('index');
});

Route::get('/src/{path}', function ($path) {
    $viewPath = str_replace('.blade.php', '', $path);
    $viewName = 'src.' . str_replace('/', '.', $viewPath);
    if (view()->exists($viewName)) {
        return view($viewName);
    }
    return abort(404);
})->where('path', '.*'); 

Route::get('/profile/tabs/{tabName}', function ($tabName) {
    $viewPath = "src.modules.feed.page.tabs.page-{$tabName}";
    if (view()->exists($viewPath)) {
        return view($viewPath)->render();
    }
    return response()->json(['error' => 'Tab not found'], 404);
});

Route::get('/auth', function () {
    return view('page.auth.auth'); 
});

// ==========================================
// CÁC ROUTE XỬ LÝ PROFILE CÁ NHÂN
// ==========================================
Route::get('/profile/{studentCode}', [ProfileController::class, 'show']);
Route::put('/profile/{studentCode}', [ProfileController::class, 'update']);
Route::post('/profile/{studentCode}/upload-image', [ProfileController::class, 'uploadImage']);
Route::post('/profile/{studentCode}/events/{eventId}/proof', [ProfileController::class, 'submitProof']);
Route::post('/profile/{studentCode}/tasks', [ProfileController::class, 'createTask']); // ĐÃ THÊM ROUTE NÀY