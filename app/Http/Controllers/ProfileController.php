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
     * Hiển thị giao diện Profile chính
     */
    public function show($studentCode)
    {
        // Lấy thông tin User cùng với khoa
        $user = User::with('faculty')->where('student_code', $studentCode)->firstOrFail();
        $faculties = Faculty::all();

        // Thống kê số lượt thích và đang theo dõi
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
     * Tải nội dung các Tab (Info, Data, Schedule, Roles) qua AJAX
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
     * Cập nhật thông tin định danh (Họ tên, Bio, SĐT, Privacy...)
     */
    public function update(Request $request, $studentCode) 
    {
        try {
            $user = User::where('student_code', $studentCode)->firstOrFail();
            // Cập nhật các trường được phép sửa đã khai báo trong fillable của Model User
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
     * Xử lý tải lên Ảnh đại diện và Ảnh bìa
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
     * LỊCH TRÌNH: Tạo mới lịch trình (Cá nhân hoặc Lịch học bổ sung)
     */
    public function createTask(Request $request, $studentCode) {
        $user = User::where('student_code', $studentCode)->firstOrFail();
        
        DB::table('user_tasks')->insert([
            'user_id' => $user->id, 
            'title' => $request->title, 
            'description' => $request->description, // Lưu mô tả chi tiết
            'type' => $request->type ?? 'task', // Phân loại: task hoặc class
            'due_date' => Carbon::parse($request->due_date),
            'is_completed' => false, 
            'created_at' => now(), 
            'updated_at' => now()
        ]);
        
        return response()->json(['success' => true]);
    }

    /**
     * LỊCH TRÌNH: Đánh dấu Hoàn thành / Chưa hoàn thành
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
     * LỊCH TRÌNH: Xóa lịch trình
     */
    public function deleteTask($studentCode, $id) {
        $user = User::where('student_code', $studentCode)->firstOrFail();
        DB::table('user_tasks')->where('id', $id)->where('user_id', $user->id)->delete();
        return response()->json(['success' => true]);
    }

    /**
     * LỊCH TRÌNH: Nộp minh chứng cho Sự kiện hoặc Lịch học
     */
    public function submitProof(Request $request, $studentCode, $type, $id) {
        $user = User::where('student_code', $studentCode)->firstOrFail();
        
        if (!$request->hasFile('proof')) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy file.'], 400);
        }

        $path = $request->file('proof')->store('uploads/proofs', 'public');
        $fileUrl = asset('storage/' . $path);
        
        if ($type === 'task') {
            // Nộp minh chứng cho lịch cá nhân/học bổ sung
            DB::table('user_tasks')->where('id', $id)->where('user_id', $user->id)
                ->update(['proof_file' => $fileUrl]);
        } else {
            // Nộp minh chứng cho sự kiện hệ thống
            DB::table('event_participants')
                ->where('event_id', $id)
                ->where('user_id', $user->id)
                ->update([
                    'proof_file' => $fileUrl, 
                    'status' => 'pending' // Chuyển trạng thái sang chờ duyệt
                ]);
        }
        
        return response()->json(['success' => true, 'url' => $fileUrl]);
    }
}