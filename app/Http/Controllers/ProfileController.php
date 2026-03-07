<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Faculty;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ProfileController extends Controller
{
    /**
     * 1. Hiển thị giao diện Profile chính
     */
    public function show($studentCode)
    {
        $user = User::with('faculty')->where('student_code', $studentCode)->firstOrFail();
        $faculties = Faculty::all();

        $totalLikes = DB::table('post_likes')
            ->join('posts', 'post_likes.post_id', '=', 'posts.id')
            ->where('posts.user_id', $user->id)
            ->count();

        $followingCount = DB::table('page_follows')
            ->where('user_id', $user->id)
            ->count();

        return view('src.modules.feed.profile.profile', compact('user', 'faculties', 'totalLikes', 'followingCount'));
    }

    /**
     * 2. Tải nội dung các Tab (Info, Data, Schedule, Roles) qua AJAX
     */
    public function getTab($studentCode, $tabName)
    {
        $user = User::with('faculty')->where('student_code', $studentCode)->firstOrFail();
        $faculties = Faculty::all();

        $viewName = "src.modules.feed.profile.tabs.profile-{$tabName}";
        if (!view()->exists($viewName)) {
            return abort(404);
        }

        return view($viewName, compact('user', 'faculties'));
    }

    /**
     * 3. Cập nhật thông tin định danh (Họ tên, Bio, SĐT, Privacy...)
     */
    public function update(Request $request, $studentCode) 
    {
        try {
            $user = User::where('student_code', $studentCode)->firstOrFail();
            $user->update($request->except(['_token']));
            
            return response()->json([
                'success' => true,
                'message' => 'Cập nhật hồ sơ thành công!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 4. Xử lý tải lên Ảnh đại diện và Ảnh bìa
     */
    public function uploadImage(Request $request, $studentCode) {
        $user = User::where('student_code', $studentCode)->firstOrFail();
        $type = $request->type;
        
        $path = $request->file('image')->store("uploads/{$type}s", 'public');
        $publicUrl = asset('storage/' . $path);
        
        if ($type === 'avatar') {
            $user->avatar = $publicUrl;
        } else if ($type === 'cover') {
            $user->cover = $publicUrl; 
        }
        
        $user->save();
        return response()->json(['success' => true, 'url' => $publicUrl]);
    }

    /**
     * 5. LỊCH TRÌNH: Tạo mới lịch trình (Cá nhân hoặc Lịch học bổ sung)
     */
    public function createTask(Request $request, $studentCode) {
        $user = User::where('student_code', $studentCode)->firstOrFail();
        
        DB::table('user_tasks')->insert([
            'user_id'         => $user->id, 
            'title'           => $request->title, 
            'description'     => $request->description,
            'type'            => $request->type ?? 'task',
            'completion_type' => $request->completion_type ?? 'simple', // Loại hoàn thành: simple hoặc proof
            'due_date'        => Carbon::parse($request->due_date),
            'is_completed'    => false, 
            'created_at'      => now(), 
            'updated_at'      => now()
        ]);
        
        return response()->json(['success' => true]);
    }

    /**
     * 6. LỊCH TRÌNH: Đánh dấu Hoàn thành / Chưa hoàn thành (Dùng cho loại "simple")
     */
    public function toggleTask($studentCode, $id) {
        $user = User::where('student_code', $studentCode)->firstOrFail();
        $task = DB::table('user_tasks')->where('id', $id)->where('user_id', $user->id)->first();

        if ($task) {
            DB::table('user_tasks')->where('id', $id)->update([
                'is_completed' => !$task->is_completed,
                'updated_at' => now()
            ]);
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false], 404);
    }

    /**
     * 7. LỊCH TRÌNH: Xóa lịch trình
     */
    public function deleteTask($studentCode, $id) {
        $user = User::where('student_code', $studentCode)->firstOrFail();
        DB::table('user_tasks')->where('id', $id)->where('user_id', $user->id)->delete();
        // Xóa luôn các proofs liên quan nếu có
        DB::table('task_proofs')->where('user_task_id', $id)->delete();
        
        return response()->json(['success' => true]);
    }

    /**
     * 8. LỊCH TRÌNH: Nộp minh chứng (Lưu ảnh + GPS + Phiên bản)
     */
    public function submitTaskProofGps(Request $request, $studentCode, $taskId) {
        $user = User::where('student_code', $studentCode)->firstOrFail();
        
        if (!$request->hasFile('proof')) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy file ảnh.'], 400);
        }

        // Lưu file vào thư mục public/uploads/proofs
        $path = $request->file('proof')->store('uploads/proofs', 'public');
        $fileUrl = asset('storage/' . $path);
        
        // Tìm số phiên bản lớn nhất hiện tại, nếu chưa có thì gán = 0
        $currentVersion = DB::table('task_proofs')->where('user_task_id', $taskId)->max('version') ?? 0;
        
        // Lưu dữ liệu vào bảng task_proofs (Thêm phiên bản mới)
        DB::table('task_proofs')->insert([
            'user_task_id' => $taskId,
            'file_url'     => $fileUrl,
            'latitude'     => $request->latitude,
            'longitude'    => $request->longitude,
            'version'      => $currentVersion + 1,
            'created_at'   => now(),
            'updated_at'   => now()
        ]);

        // Cập nhật trạng thái task thành "Đã hoàn thành"
        DB::table('user_tasks')->where('id', $taskId)->where('user_id', $user->id)->update([
            'is_completed' => true,
            'updated_at'   => now()
        ]);

        return response()->json(['success' => true, 'url' => $fileUrl]);
    }
    /**
     * THỜI KHÓA BIỂU: Tạo môn học lặp lại hàng tuần
     */
    public function createClass(Request $request, $studentCode) {
        $user = User::where('student_code', $studentCode)->firstOrFail();
        
        DB::table('user_classes')->insert([
            'user_id'     => $user->id,
            'title'       => $request->title,
            'day_of_week' => $request->day_of_week,
            'start_time'  => $request->start_time,
            'end_time'    => $request->end_time,
            'location'    => $request->location,
            'color'       => '#23a559',
            'created_at'  => now(),
            'updated_at'  => now()
        ]);
        
        return response()->json(['success' => true]);
    }

    /**
     * THỜI KHÓA BIỂU: Xóa môn học
     */
    public function deleteClass($studentCode, $id) {
        $user = User::where('student_code', $studentCode)->firstOrFail();
        DB::table('user_classes')->where('id', $id)->where('user_id', $user->id)->delete();
        return response()->json(['success' => true]);
    }
}