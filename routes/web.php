<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Modules\Form\Controllers\FormController;
use App\Modules\Post\Controllers\PostController;
use App\Modules\Event\Controllers\EventController;

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

// --- A. CÁC ROUTE TĨNH (PHẢI ĐẶT LÊN TRÊN CÙNG) ---
// Hiển thị giao diện Tạo Form
Route::get('/forms/create', [FormController::class, 'create'])->name('forms.create');
// Nhận dữ liệu lưu Form vào Database
Route::post('/forms', [FormController::class, 'store'])->name('forms.store');


// --- B. CÁC ROUTE ĐỘNG CÓ {form} (ĐẶT XUỐNG DƯỚI) ---
// Route hiển thị giao diện Form cho người dùng xem và điền
Route::get('/forms/{form}', [FormController::class, 'show'])->name('forms.show');
// Route xử lý khi người dùng bấm nút "Nộp Form"
Route::post('/forms/{form}/submit', [FormController::class, 'submit'])->name('forms.submit');
// Route cho Ban tổ chức xem danh sách người đã nộp đơn
Route::get('/forms/{form}/submissions', [FormController::class, 'submissions'])->name('forms.submissions');
// Route xuất dữ liệu ra file Excel (CSV)
Route::get('/forms/{form}/export', [FormController::class, 'export'])->name('forms.export');


// --- C. ROUTE XỬ LÝ ĐƠN TỪ (SUBMISSIONS) ---
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

// ==========================================
// CÁC ROUTE XỬ LÝ BÀI VIẾT (POSTS)
// ==========================================

// Route nhận dữ liệu bài viết mới gửi lên từ Form
Route::post('/posts', [PostController::class, 'store'])->name('posts.store');

// ==========================================
// CÁC ROUTE XỬ LÝ SỰ KIỆN (EVENTS)
// ==========================================

// Route tĩnh
Route::get('/events/create', [EventController::class, 'create'])->name('events.create');
Route::post('/events', [EventController::class, 'store'])->name('events.store');

// Route động
Route::get('/events/{event}', [EventController::class, 'show'])->name('events.show');
Route::post('/events/{event}/join', [EventController::class, 'join'])->name('events.join');

use App\Models\Event;
use Carbon\Carbon;

// BÍ KÍP TEST: Route tạm để tự động tạo 1 Sự kiện mẫu vào DB
Route::get('/create-dummy-event', function () {
    $event = Event::create([
        'title'       => 'Sự kiện Giao lưu Sinh viên IT 2026',
        'description' => 'Đây là sự kiện mẫu để test luồng tham gia. Sinh viên sẽ được giao lưu với các cựu sinh viên xuất sắc.',
        'start_time'  => Carbon::now()->addDays(3)->setHour(8)->setMinute(0), // Diễn ra vào 8h sáng, 3 ngày sau
        'end_time'    => Carbon::now()->addDays(3)->setHour(11)->setMinute(30),
        'location'    => 'Hội trường D1, Tòa nhà trung tâm',
        // 'page_id'  => 1 // (Bỏ dấu // ở đầu nếu DB của bạn bắt buộc sự kiện phải thuộc về 1 Fanpage)
    ]);

    return "🎉 Đã tạo sự kiện thành công! ID của sự kiện là: <b>{$event->id}</b> <br><br> 
            <a href='/events/{$event->id}' style='padding: 10px 20px; background: #1877f2; color: white; text-decoration: none; border-radius: 6px;'>
                Bấm vào đây để xem giao diện Sự kiện luôn!
            </a>";
});