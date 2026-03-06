<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdvancedScheduleSeeder extends Seeder
{
    public function run()
    {
        $now = Carbon::now();
        $user = DB::table('users')->where('student_code', '27A4043284')->first();
        if (!$user) return;

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('event_participants')->truncate();
        DB::table('events')->truncate();
        DB::table('user_tasks')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 1. Tạo Sự kiện
        $eventId1 = DB::table('events')->insertGetId([
            'title' => 'UIX Studio Meeting', 'description' => 'Họp thiết kế',
            'start_time' => $now->copy()->setTime(11, 0), 'end_time' => $now->copy()->setTime(11, 30),
            'location' => 'Phòng họp Google Meet', 'created_at' => $now, 'updated_at' => $now
        ]);
        
        $eventId2 = DB::table('events')->insertGetId([
            'title' => 'Hệ quản trị CSDL (Ca 2)', 'description' => 'Lên lớp',
            'start_time' => $now->copy()->setTime(14, 0), 'end_time' => $now->copy()->setTime(16, 30),
            'location' => 'Phòng 302 - D3', 'created_at' => $now, 'updated_at' => $now
        ]);

        DB::table('event_participants')->insert([
            ['user_id' => $user->id, 'event_id' => $eventId1, 'status' => 'going', 'proof_status' => 'none', 'created_at' => $now, 'updated_at' => $now],
            ['user_id' => $user->id, 'event_id' => $eventId2, 'status' => 'going', 'proof_status' => 'pending', 'created_at' => $now, 'updated_at' => $now],
        ]);

        // 2. Tạo Lịch cá nhân (Task)
        DB::table('user_tasks')->insert([
            [
                'user_id' => $user->id, 'title' => 'Code xong Tab Lịch trình (Xanh đỏ tím vàng)', 
                'is_completed' => false, 'due_date' => $now->copy()->setTime(12, 0),
                'created_at' => $now, 'updated_at' => $now
            ]
        ]);
    }
}