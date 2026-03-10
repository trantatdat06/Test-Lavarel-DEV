<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Modules\Form\Controllers\FormController;
use App\Modules\Post\Controllers\PostController;

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
// CÁC ROUTE XỬ LÝ FORM ĐĂNG KÝ / KHẢO SÁT
// ==========================================

// Route cho Ban tổ chức tạo Form mới (Dùng phương thức POST để gửi dữ liệu ẩn)
Route::post('/forms/store', [FormController::class, 'store'])->name('forms.store');

// Route hiển thị giao diện Form cho người dùng xem và điền (Dùng GET để lấy giao diện)
Route::get('/forms/{form}', [FormController::class, 'show'])->name('forms.show');

// Route xử lý khi người dùng bấm nút "Nộp Form"
Route::post('/forms/{form}/submit', [FormController::class, 'submit'])->name('forms.submit');

// Route cho Ban tổ chức xem danh sách người đã nộp đơn
Route::get('/forms/{form}/submissions', [FormController::class, 'submissions'])->name('forms.submissions');

// Route cho Ban tổ chức duyệt/từ chối đơn (Cập nhật trạng thái)
Route::put('/submissions/{submission}/update', [FormController::class, 'updateSubmission'])->name('submissions.update');


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
// Route xóa lịch trình
Route::delete('/profile/{studentCode}/tasks/{id}', [ProfileController::class, 'deleteTask']);
// Route nộp minh chứng (dùng cho cả Sự kiện và Lịch học bổ sung)
Route::post('/profile/{studentCode}/submit-proof/{type}/{id}', [ProfileController::class, 'submitProof']);
Route::post('/profile/{studentCode}/tasks/{taskId}/proof-gps', [\App\Http\Controllers\ProfileController::class, 'submitTaskProofGps']);
Route::post('/profile/{studentCode}/classes', [\App\Http\Controllers\ProfileController::class, 'createClass']);
Route::delete('/profile/{studentCode}/classes/{id}', [\App\Http\Controllers\ProfileController::class, 'deleteClass']);

// Tuyến đường nhận dữ liệu Đăng Bài
Route::post('/posts', [PostController::class, 'store']);