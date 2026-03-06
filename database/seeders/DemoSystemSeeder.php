<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str; // FIX: Thêm thư viện tạo Slug
use Carbon\Carbon;

class DemoSystemSeeder extends Seeder
{
    public function run()
    {
        // 0. Dọn dẹp dữ liệu cũ của Page và Post để tránh lỗi Duplicate khi chạy lệnh nhiều lần
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('posts')->truncate();
        DB::table('pages')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $now = Carbon::now();
        $password = Hash::make('Password@123'); 

        $privacy = json_encode([
            'full_name' => 'public', 'bio' => 'public', 'job' => 'public',
            'faculty' => 'public', 'class_name' => 'friends', 'phone' => 'private',
            'gender' => 'public', 'dob' => 'friends', 'social_links' => 'public'
        ]);

        // ==========================================
        // 1. TẠO 4 USER MẪU
        // ==========================================
        $users = [
            [
                'student_code' => '27A4043284', 'email' => '27A4043284@hvnh.edu.vn',
                'full_name' => 'Trần Tất Đạt', 'password' => $password, 'phone' => '0987654321',
                'gender' => 'Nam', 'dob' => '2005-01-20', 'job' => 'IT Business Analyst',
                'social_links' => 'github.com/dat-tran', 'faculty_id' => 5, 'class_name' => 'K27HTTTB',
                'bio' => 'Trưởng nhóm phát triển Hệ sinh thái Mạng xã hội Học tập tích hợp AI.',
                'avatar' => 'https://ui-avatars.com/api/?name=Dat+Tran&background=1877f2&color=fff&size=150',
                'cover' => 'https://images.unsplash.com/photo-1555066931-4365d14bab8c?q=80&w=1000',
                'privacy_settings' => $privacy, 'role' => 'super_admin',
                'created_at' => $now, 'updated_at' => $now
            ],
            [
                'student_code' => 'GV001', 'email' => 'huyen.gtt@hvnh.edu.vn',
                'full_name' => 'Giang Thị Thu Huyền', 'password' => $password, 'phone' => '0912345678',
                'gender' => 'Nữ', 'dob' => '1990-05-15', 'job' => 'Giảng viên',
                'social_links' => 'linkedin.com/in/huyen-gtt', 'faculty_id' => 5, 'class_name' => 'Khoa CNTT',
                'bio' => 'Giảng viên khoa Hệ thống Thông tin - ITDE.',
                'avatar' => 'https://ui-avatars.com/api/?name=Thu+Huyen&background=e83e8c&color=fff&size=150',
                'cover' => 'https://images.unsplash.com/photo-1524178232363-1fb2b075b655?q=80&w=1000',
                'privacy_settings' => $privacy, 'role' => 'page_admin',
                'created_at' => $now, 'updated_at' => $now
            ],
            [
                'student_code' => 'SV002', 'email' => 'sv002@hvnh.edu.vn',
                'full_name' => 'Nguyễn Thị Bích Ngọc', 'password' => $password, 'phone' => '0933445566',
                'gender' => 'Nữ', 'dob' => '2004-10-10', 'job' => 'Thực tập sinh Marketing',
                'social_links' => 'facebook.com/bichngoc', 'faculty_id' => 4, 'class_name' => 'K26QTKDA',
                'bio' => 'Thích đọc sách và tham gia các hoạt động ngoại khóa.',
                'avatar' => 'https://ui-avatars.com/api/?name=Bich+Ngoc&background=28a745&color=fff&size=150',
                'cover' => 'https://images.unsplash.com/photo-1501504905252-473c47e087f8?q=80&w=1000',
                'privacy_settings' => $privacy, 'role' => 'user',
                'created_at' => $now, 'updated_at' => $now
            ],
            [
                'student_code' => 'SV003', 'email' => 'sv003@hvnh.edu.vn',
                'full_name' => 'Lê Minh Tuấn', 'password' => $password, 'phone' => '0977889900',
                'gender' => 'Nam', 'dob' => '2003-08-22', 'job' => 'Ban truyền thông',
                'social_links' => 'tiktok.com/@minhtuan', 'faculty_id' => 1, 'class_name' => 'K25NHA',
                'bio' => 'Nhiếp ảnh gia nghiệp dư.',
                'avatar' => 'https://ui-avatars.com/api/?name=Minh+Tuan&background=fd7e14&color=fff&size=150',
                'cover' => 'https://images.unsplash.com/photo-1451187580459-43490279c0fa?q=80&w=1000',
                'privacy_settings' => $privacy, 'role' => 'user',
                'created_at' => $now, 'updated_at' => $now
            ]
        ];

        foreach ($users as $u) {
            DB::table('users')->updateOrInsert(['email' => $u['email']], $u);
        }

        $adminId = DB::table('users')->where('email', '27A4043284@hvnh.edu.vn')->value('id');
        $gvId = DB::table('users')->where('email', 'huyen.gtt@hvnh.edu.vn')->value('id');

        // ==========================================
        // 2. TẠO PAGE GỐC & PAGE CON
        // ==========================================
        $mainPageName = 'Học viện Ngân hàng';
        $mainPageId = DB::table('pages')->insertGetId([
            'name' => $mainPageName,
            'slug' => Str::slug($mainPageName), // FIX: Tự động tạo slug (hoc-vien-ngan-hang)
            'description' => 'Trang thông tin chính thức của Học viện Ngân hàng.',
            'avatar' => 'https://ui-avatars.com/api/?name=HV&background=1877f2&color=fff',
            'cover' => 'https://images.unsplash.com/photo-1562774053-701939374585?q=80&w=1000',
            'parent_id' => null,
            'created_by' => $adminId, 
            'created_at' => $now, 'updated_at' => $now
        ]);

        $subPages = [
            ['name' => 'Khoa Công nghệ Thông tin', 'desc' => 'ITDE - Banking Academy', 'parent_id' => $mainPageId],
            ['name' => 'Khoa Ngân hàng', 'desc' => 'Khoa chuyên ngành mũi nhọn của trường.', 'parent_id' => $mainPageId],
            ['name' => 'CLB Tin học Ngân hàng - BIT', 'desc' => 'Nơi kết nối đam mê công nghệ.', 'parent_id' => $mainPageId],
            ['name' => 'Đoàn Thanh niên HVNH', 'desc' => 'Tổ chức các phong trào sinh viên.', 'parent_id' => $mainPageId],
            ['name' => 'Phòng Đào tạo', 'desc' => 'Hỗ trợ thủ tục học vụ, đăng ký tín chỉ.', 'parent_id' => $mainPageId],
        ];

        $pageIds = [$mainPageId];
        foreach ($subPages as $sp) {
            $pageIds[] = DB::table('pages')->insertGetId([
                'name' => $sp['name'],
                'slug' => Str::slug($sp['name']), // FIX: Tự động tạo slug
                'description' => $sp['desc'],
                'avatar' => 'https://ui-avatars.com/api/?name='.urlencode($sp['name']).'&background=random&color=fff',
                'cover' => 'https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?q=80&w=1000',
                'parent_id' => $sp['parent_id'],
                'created_by' => $adminId, 
                'created_at' => $now, 'updated_at' => $now
            ]);
        }

        // ==========================================
        // 3. TẠO BÀI VIẾT (POSTS) CHO CÁC PAGE
        // ==========================================
        $postContents = [
            "Thông báo: Lịch nghỉ lễ và học bù tuần tới. Sinh viên chú ý theo dõi.",
            "Workshop: Ứng dụng AI trong học tập. Đăng ký tham gia ngay để nhận chứng chỉ!",
            "Chúc mừng các bạn sinh viên đã hoàn thành xuất sắc đợt bảo vệ đồ án vừa qua.",
            "Tuyển thành viên Ban Truyền thông nhiệm kỳ 2026. Cơ hội rèn luyện kỹ năng mềm.",
            "Tài liệu ôn thi cuối kỳ môn Cơ sở dữ liệu đã được cập nhật. Các bạn truy cập link đính kèm."
        ];

        $posts = [];
        foreach ($pageIds as $pId) {
            for ($i = 0; $i < 4; $i++) {
                $posts[] = [
                    'page_id' => $pId,
                    'user_id' => ($i % 2 == 0) ? $adminId : $gvId,
                    'content' => $postContents[array_rand($postContents)] . " (Post #" . rand(100, 999) . ")",
                    'created_at' => $now->copy()->subDays(rand(0, 10)), 
                    'updated_at' => $now
                ];
            }
        }

        DB::table('posts')->insert($posts);
    }
}