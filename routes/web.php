<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;

// 1. Route chính mở trang chủ
Route::get('/', function () {
    return view('index');
});

// 2. Route xử lý việc tải các file giao diện động (Tabs)
Route::get('/src/{path}', function ($path) {
    $viewPath = str_replace('.blade.php', '', $path);
    $viewName = 'src.' . str_replace('/', '.', $viewPath);
    if (view()->exists($viewName)) {
        return view($viewName);
    }
    return abort(404);
})->where('path', '.*'); 

// 3. Route trang Đăng nhập / Đăng ký
Route::get('/auth', function () {
    return view('page.auth.auth'); 
});

// ==========================================
// CÁC ROUTE XỬ LÝ PROFILE CÁ NHÂN (HỒ SƠ)
// ==========================================

// Lấy dữ liệu profile
Route::get('/profile/{studentCode}', [ProfileController::class, 'show']);

// Cập nhật thông tin hồ sơ
Route::put('/profile/{studentCode}', [ProfileController::class, 'update']);

// Upload Ảnh Avatar và Cover
Route::post('/profile/{studentCode}/upload-image', [ProfileController::class, 'uploadImage']);

// Nộp minh chứng cho sự kiện
Route::post('/profile/{studentCode}/events/{eventId}/proof', [ProfileController::class, 'submitProof']);

// Tạo lịch cá nhân / To-do
Route::post('/profile/{studentCode}/tasks', [ProfileController::class, 'createTask']);
// Route chuyên dụng để load nội dung từng Tab trong Profile
Route::get('/profile/{studentCode}/tab/{tabName}', [ProfileController::class, 'getTab']);
// Route để thay đổi trạng thái hoàn thành của task
Route::post('/profile/{studentCode}/tasks/{id}/toggle', [ProfileController::class, 'toggleTask']);
