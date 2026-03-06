<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DemoSystemSeeder extends Seeder
{
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('users')->truncate();
        DB::table('pages')->truncate();
        DB::table('user_tasks')->truncate();
        DB::table('event_participants')->truncate();
        DB::table('events')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $now = Carbon::now();

        // Tạo User Admin Đạt
        $adminId = DB::table('users')->insertGetId([
            'student_code' => '27A4043284', 'email' => '27A4043284@hvnh.edu.vn', 'full_name' => 'Trần Tất Đạt',
            'password' => Hash::make('Password@123'), 'phone' => '0987654321', 'faculty_id' => 5,
            'class_name' => 'K27HTTTB', 'created_at' => $now, 'updated_at' => $now
        ]);

        // Tạo sự kiện trường
        $eventId = DB::table('events')->insertGetId([
            'title' => 'Ngày hội Việc làm BAV 2026', 'description' => 'Tuyển dụng',
            'start_time' => $now->copy()->setTime(8, 0), 'end_time' => $now->copy()->setTime(17, 0),
            'location' => 'Sân vận động BAV', 'created_at' => $now, 'updated_at' => $now
        ]);

        DB::table('event_participants')->insert([
            ['user_id' => $adminId, 'event_id' => $eventId, 'status' => 'going', 'proof_status' => 'none', 'created_at' => $now, 'updated_at' => $now]
        ]);
        
        // Tạo lịch cá nhân mẫu
        DB::table('user_tasks')->insert([
            ['user_id' => $adminId, 'title' => 'Nộp báo cáo Phase 1', 'is_completed' => false, 'due_date' => $now->copy()->setTime(23, 59), 'created_at' => $now, 'updated_at' => $now]
        ]);
    }
}