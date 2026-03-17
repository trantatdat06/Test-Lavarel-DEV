<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Faculty;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Str;

class ProfileController extends Controller
{
    /**
     * 1. Hiển thị giao diện Profile chính
     */
    public function show($studentCode)
    {
        $user = User::where('student_code', $studentCode)->firstOrFail();
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
     * 2. Tải nội dung các Tab (AJAX) - ĐÃ FIX LỖI ĐƯỜNG DẪN VÀ DỮ LIỆU
     */
    public function getTab($studentCode, $tabName)
    {
        // Sử dụng DB::table để tránh lỗi nếu Model User chưa định nghĩa quan hệ
        $user = DB::table('users')->where('student_code', $studentCode)->first();
        
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $faculties = DB::table('faculties')->get();
        $viewName = "src.modules.feed.profile.tabs.profile-{$tabName}";

        if (!view()->exists($viewName)) {
            return abort(404);
        }

        return view($viewName, compact('user', 'faculties'));
    }

    /**
     * 3. Cập nhật thông tin hồ sơ
     */
    public function update(Request $request, $studentCode) 
    {
        try {
            $user = User::where('student_code', $studentCode)->firstOrFail();
            $user->update($request->except(['_token']));
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false], 500);
        }
    }

    /**
     * 4. Xử lý tải lên Ảnh đại diện và Ảnh bìa
     */
    public function uploadImage(Request $request, $studentCode) {
        $user = User::where('student_code', $studentCode)->firstOrFail();
        $path = $request->file('image')->store("uploads/{$request->type}s", 'public');
        $url = asset('storage/' . $path);
        if ($request->type === 'avatar') $user->avatar = $url;
        else $user->cover = $url;
        $user->save();
        return response()->json(['success' => true, 'url' => $url]);
    }

    /**
     * 5. LỊCH TRÌNH: Các hàm xử lý Task (Giữ nguyên)
     */
    public function createTask(Request $request, $studentCode) {
        $user = User::where('student_code', $studentCode)->firstOrFail();
        DB::table('user_tasks')->insert([
            'user_id' => $user->id, 'title' => $request->title, 'description' => $request->description,
            'type' => $request->type ?? 'task', 'completion_type' => $request->completion_type ?? 'simple',
            'due_date' => Carbon::parse($request->due_date), 'is_completed' => false, 'created_at' => now(), 'updated_at' => now()
        ]);
        return response()->json(['success' => true]);
    }

    public function toggleTask($studentCode, $id) {
        $user = User::where('student_code', $studentCode)->firstOrFail();
        $task = DB::table('user_tasks')->where('id', $id)->where('user_id', $user->id)->first();
        if ($task) {
            DB::table('user_tasks')->where('id', $id)->update(['is_completed' => !$task->is_completed, 'updated_at' => now()]);
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false], 404);
    }

    public function deleteTask($studentCode, $id) {
        $user = User::where('student_code', $studentCode)->firstOrFail();
        DB::table('user_tasks')->where('id', $id)->where('user_id', $user->id)->delete();
        DB::table('task_proofs')->where('user_task_id', $id)->delete();
        return response()->json(['success' => true]);
    }

    public function submitTaskProofGps(Request $request, $studentCode, $taskId) {
        $user = User::where('student_code', $studentCode)->firstOrFail();
        $path = $request->file('proof')->store('uploads/proofs', 'public');
        $url = asset('storage/' . $path);
        $v = DB::table('task_proofs')->where('user_task_id', $taskId)->max('version') ?? 0;
        DB::table('task_proofs')->insert([
            'user_task_id' => $taskId, 'file_url' => $url, 'latitude' => $request->latitude,
            'longitude' => $request->longitude, 'version' => $v + 1, 'created_at' => now(), 'updated_at' => now()
        ]);
        DB::table('user_tasks')->where('id', $taskId)->update(['is_completed' => true, 'updated_at' => now()]);
        return response()->json(['success' => true, 'url' => $url]);
    }

    /**
     * 6. THỜI KHÓA BIỂU (Giữ nguyên)
     */
    public function createClass(Request $request, $studentCode) {
        $user = User::where('student_code', $studentCode)->firstOrFail();
        DB::table('user_classes')->insert([
            'user_id' => $user->id, 'title' => $request->title, 'day_of_week' => $request->day_of_week,
            'start_time' => $request->start_time, 'end_time' => $request->end_time, 'location' => $request->location,
            'color' => '#23a559', 'created_at' => now(), 'updated_at' => now()
        ]);
        return response()->json(['success' => true]);
    }

    public function deleteClass($studentCode, $id) {
        $user = User::where('student_code', $studentCode)->firstOrFail();
        DB::table('user_classes')->where('id', $id)->where('user_id', $user->id)->delete();
        return response()->json(['success' => true]);
    }

    /**
     * 7. VAI TRÒ: Gửi yêu cầu tạo Trang (Page) mới
     */
    public function submitPageRequest(Request $request, $studentCode) {
        $user = User::where('student_code', $studentCode)->firstOrFail();

        // Kiểm tra lượt còn lại (3 lượt)
        if ($user->upgrade_attempt_count <= 0) {
            return response()->json(['success' => false, 'message' => 'Bạn đã hết lượt yêu cầu và tính năng bị khóa!'], 403);
        }

        // Tạo Page pending
        $pageId = DB::table('pages')->insertGetId([
            'name' => $request->name,
            'slug' => Str::slug($request->name) . '-' . time(),
            'category' => $request->category,
            'description' => $request->description,
            'status' => 'pending', 
            'created_by' => $user->id,
            'type' => 'public',
            'created_at' => now(), 'updated_at' => now(),
        ]);

        // Gán quyền admin pending
        DB::table('page_members')->insert([
            'page_id' => $pageId, 'user_id' => $user->id, 'role' => 'admin', 'status' => 'pending', 
            'created_at' => now(), 'updated_at' => now(),
        ]);

        return response()->json(['success' => true]);
    }
}