<?php

use Illuminate\Support\Facades\Route;

// 1. Route chính mở trang chủ
Route::get('/', function () {
    return view('index');
});

// 2. Route "thông chốt" cho tất cả các lệnh fetch() gọi tới thư mục src/
Route::get('/src/{path}', function ($path) {
    // Xóa đuôi .blade.php khỏi đường dẫn yêu cầu
    $viewPath = str_replace('.blade.php', '', $path);
    
    // Đổi dấu gạch chéo (/) thành dấu chấm (.) chuẩn cú pháp view của Laravel
    $viewName = 'src.' . str_replace('/', '.', $viewPath);
    
    // Nếu file tồn tại trong resources/views/ thì trả về HTML cho JavaScript
    if (view()->exists($viewName)) {
        return view($viewName);
    }
    
    // Nếu không tìm thấy thì báo lỗi 404
    return abort(404);
})->where('path', '.*'); // Cho phép tham số path chứa nhiều thư mục lồng nhau

// Trong routes/web.php
Route::get('/profile/tabs/{tabName}', function ($tabName) {
    // Kiểm tra file view có tồn tại không trước khi trả về
    $viewPath = "src.modules.feed.page.tabs.page-{$tabName}";
    
    if (view()->exists($viewPath)) {
        return view($viewPath)->render();
    }
    
    return response()->json(['error' => 'Tab not found'], 404);
});

Route::get('/auth', function () {
    // Trỏ đến file resources/views/page/auth/auth.blade.php
    return view('page.auth.auth'); 
});