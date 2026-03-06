<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class EventScheduleSeeder extends Seeder
{
    public function run()
    {
        $now = Carbon::now();
        
        // Lấy ID của Admin (Đạt)
        $user = DB::table('users')->where('student_code', '27A4043284')->first();
        if (!$user) return;

        // Dọn dẹp dữ liệu cũ để tránh rác khi chạy nhiều lần
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('event_participants')->truncate();
        DB::table('events')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 1. Tạo 3 sự kiện mẫu (ĐÃ XÓA CỘT 'status' GÂY LỖI)
        $eventId1 = DB::table('events')->insertGetId([
            'title' => 'Bảo vệ Phase 1 Dự án Mạng xã hội AI',
            'description' => 'Trình bày tiến độ dự án mạng xã hội học tập.',
            'start_time' => $now->copy()->addHours(2),
            'end_time' => $now->copy()->addHours(3),
            'location' => 'Phòng 201 - Tòa B',
            'created_at' => $now, 'updated_at' => $now
        ]);

        $eventId2 = DB::table('events')->insertGetId([
            'title' => 'Workshop: Khởi nghiệp Kỷ nguyên số',
            'description' => 'Khách mời từ các doanh nghiệp công nghệ.',
            'start_time' => $now->copy()->addDays(1)->setTime(8, 30),
            'end_time' => $now->copy()->addDays(1)->setTime(11, 0),
            'location' => 'Hội trường lớn BAV',
            'created_at' => $now, 'updated_at' => $now
        ]);

        $eventId3 = DB::table('events')->insertGetId([
            'title' => 'Tiếng Anh chuyên ngành (Thi giữa kỳ)',
            'description' => 'Thi trên máy tính.',
            'start_time' => $now->copy()->addDays(2)->setTime(13, 0),
            'end_time' => $now->copy()->addDays(2)->setTime(15, 0),
            'location' => 'Phòng Máy 3 - Thư viện',
            'created_at' => $now, 'updated_at' => $now
        ]);

        // 2. Cho user (Đạt) đăng ký tham gia 3 sự kiện này
        DB::table('event_participants')->insert([
            ['user_id' => $user->id, 'event_id' => $eventId1, 'status' => 'going', 'proof_status' => 'none', 'created_at' => $now, 'updated_at' => $now],
            ['user_id' => $user->id, 'event_id' => $eventId2, 'status' => 'going', 'proof_status' => 'none', 'created_at' => $now, 'updated_at' => $now],
            ['user_id' => $user->id, 'event_id' => $eventId3, 'status' => 'going', 'proof_status' => 'none', 'created_at' => $now, 'updated_at' => $now],
        ]);
    }
}