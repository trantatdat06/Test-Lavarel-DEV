<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Event;
use App\Models\Faculty;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ProfileController extends Controller
{
    public function show($studentCode)
    {
        $user = User::with('faculty')->where('student_code', $studentCode)->firstOrFail();
        $faculties = Faculty::all();

        // 1. LẤY SỰ KIỆN TRƯỜNG (Loại: event)
        $events = DB::table('event_participants')
            ->join('events', 'event_participants.event_id', '=', 'events.id')
            ->where('event_participants.user_id', $user->id)
            ->where('event_participants.status', 'going')
            ->select('events.id', 'events.title', 'events.start_time', 'events.end_time', 'events.location', 'event_participants.proof_status')
            ->get()->map(function($e) {
                return [
                    'type' => 'event', 'id' => $e->id, 'title' => $e->title,
                    'start' => Carbon::parse($e->start_time)->toIso8601String(),
                    'timeLabel' => Carbon::parse($e->start_time)->format('H:i'),
                    'timeDetail' => Carbon::parse($e->start_time)->format('H:i') . ' - ' . Carbon::parse($e->end_time)->format('H:i'),
                    'location' => $e->location ?? 'Hội trường BAV', 'status' => $e->proof_status, 'color' => '#6b4ce6' 
                ];
            });

        // 2. LẤY LỊCH CÁ NHÂN (Loại: task)
        $tasks = DB::table('user_tasks')
            ->where('user_id', $user->id)
            ->select('id', 'title', 'due_date', 'is_completed')
            ->get()->map(function($t) {
                $time = Carbon::parse($t->due_date);
                return [
                    'type' => 'task', 'id' => $t->id, 'title' => $t->title,
                    'start' => $time->toIso8601String(),
                    'timeLabel' => $time->format('H:i'),
                    'timeDetail' => $time->format('H:i') . ' (Lịch cá nhân)',
                    'location' => 'Việc cá nhân', 'status' => $t->is_completed ? 'completed' : 'pending', 'color' => '#e84c6c' 
                ];
            });

        // 3. TẠO DỮ LIỆU MẪU LỊCH HỌC (Loại: class)
        $classes = collect([
            [
                'type' => 'class', 'id' => 999, 'title' => 'Lập trình Web nâng cao (Phòng 402)',
                'start' => Carbon::now()->setTime(13, 0)->toIso8601String(),
                'timeLabel' => '13:00', 'timeDetail' => '13:00 - 15:30',
                'location' => 'Giảng đường D2', 'status' => 'going', 'color' => '#23a559'
            ]
        ]);
            
        // Gộp cả 3 loại
        $schedule = $events->concat($tasks)->concat($classes)->sortBy('start')->values()->all();

        $profileData = [
            'name' => $user->full_name, 'msv' => $user->student_code, 'email' => $user->email,
            'phone' => $user->phone ?? '', 'gender' => $user->gender ?? '', 'dob' => $user->dob ?? '',
            'job' => $user->job ?? '', 'social_links' => $user->social_links ?? '',
            'className' => $user->class_name ?? '', 'faculty_id' => $user->faculty_id,
            'faculty' => $user->faculty ? $user->faculty->name : '', 'bio' => $user->bio ?? '',
            'avatar' => $user->avatar ?? "https://ui-avatars.com/api/?name=".urlencode($user->full_name)."&background=4a66f0&color=fff",
            'cover' => $user->cover ?? "https://images.unsplash.com/photo-1557683316-973673baf926?q=80&w=1000",
            'privacy' => json_decode($user->privacy_settings, true) ?? [],
            'schedule' => $schedule,
            'managedPages' => DB::table('pages')->where('created_by', $user->id)->get(),
            'savedPosts' => []
        ];

        return view('src.modules.feed.profile.profile', ['profileData' => json_encode($profileData), 'faculties' => $faculties]);
    }

    public function update(Request $request, $studentCode) {
        $user = User::where('student_code', $studentCode)->firstOrFail();
        $user->update($request->except(['_token']));
        return response()->json(['success' => true]);
    }

    public function uploadImage(Request $request, $studentCode) {
        $user = User::where('student_code', $studentCode)->firstOrFail();
        $type = $request->type;
        $path = $request->file('image')->store("uploads/{$type}s", 'public');
        $publicUrl = asset('storage/' . $path);
        if ($type === 'avatar') $user->avatar = $publicUrl; else $user->cover = $publicUrl;
        $user->save();
        return response()->json(['success' => true, 'url' => $publicUrl]);
    }

    public function submitProof(Request $request, $studentCode, $eventId) {
        $user = User::where('student_code', $studentCode)->firstOrFail();
        $path = $request->file('proof_file')->store("uploads/proofs", 'public');
        DB::table('event_participants')->where('user_id', $user->id)->where('event_id', $eventId)
            ->update(['proof_file' => asset('storage/' . $path), 'proof_note' => $request->proof_note, 'proof_status' => 'pending']);
        return response()->json(['success' => true, 'message' => 'Nộp minh chứng thành công!']);
    }

    public function createTask(Request $request, $studentCode) {
        $user = User::where('student_code', $studentCode)->firstOrFail();
        $request->validate(['title' => 'required|string|max:255', 'due_date' => 'required']);
        DB::table('user_tasks')->insert([
            'user_id' => $user->id, 'title' => $request->title, 'due_date' => Carbon::parse($request->due_date),
            'is_completed' => false, 'created_at' => now(), 'updated_at' => now()
        ]);
        return response()->json(['success' => true, 'message' => 'Đã thêm lịch trình thành công!']);
    }
}