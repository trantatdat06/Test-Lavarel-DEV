<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Page;
use App\Models\PageMember;
use App\Models\Post;
use App\Models\Faculty;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── 1. Faculties ──────────────────────────────────
        $faculties = [
            ['name' => 'Khoa Ngân hàng',            'code' => 'NH'],
            ['name' => 'Khoa Tài chính',             'code' => 'TC'],
            ['name' => 'Khoa Kế toán – Kiểm toán',  'code' => 'KT'],
            ['name' => 'Khoa Quản trị Kinh doanh',  'code' => 'QTKD'],
            ['name' => 'Khoa Hệ thống Thông tin',   'code' => 'HTTT'],
            ['name' => 'Khoa Luật Kinh tế',         'code' => 'LKT'],
        ];

        foreach ($faculties as $f) {
            Faculty::firstOrCreate(['code' => $f['code']], $f);
        }

        $facultyHttt = Faculty::where('code', 'HTTT')->first();
        $facultyNh   = Faculty::where('code', 'NH')->first();

        // ── 2. Users ──────────────────────────────────────
        $admin = User::firstOrCreate(['email' => 'admin@hvnh.edu.vn'], [
            'student_code'      => 'ADMIN001',
            'full_name'         => 'Super Admin',
            'display_name'      => 'Admin',
            'password'          => Hash::make('Admin@12345!'),
            'role'              => 'super_admin',
            'first_login'       => false,
            'faculty_id'        => $facultyHttt?->id,
            'email_verified_at' => now(),
        ]);

        $user1 = User::firstOrCreate(['email' => 'sv001@hvnh.edu.vn'], [
            'student_code'      => 'SV001',
            'full_name'         => 'Nguyễn Quang Duy',
            'display_name'      => 'Quang Duy',
            'password'          => Hash::make('SinhVien@123!'),
            'role'              => 'page_admin',
            'first_login'       => false,
            'faculty_id'        => $facultyHttt?->id,
            'email_verified_at' => now(),
        ]);

        $user2 = User::firstOrCreate(['email' => 'sv002@hvnh.edu.vn'], [
            'student_code'      => 'SV002',
            'full_name'         => 'Nguyễn Thị Thanh Hoài',
            'display_name'      => 'Thanh Hoài',
            'password'          => Hash::make('SinhVien@123!'),
            'role'              => 'user',
            'first_login'       => false,
            'faculty_id'        => $facultyNh?->id,
            'email_verified_at' => now(),
        ]);

        $user3 = User::firstOrCreate(['email' => 'sv003@hvnh.edu.vn'], [
            'student_code'      => 'SV003',
            'full_name'         => 'Lê Minh Khoa',
            'display_name'      => 'Minh Khoa',
            'password'          => Hash::make('SinhVien@123!'),
            'role'              => 'user',
            'first_login'       => false,
            'faculty_id'        => $facultyHttt?->id,
            'email_verified_at' => now(),
        ]);

        // ── 3. Pages ──────────────────────────────────────
        $pages = [
            [
                'name'        => 'BCH Hội Sinh viên HVNH',
                'slug'        => 'hoi-sinh-vien-hvnh',
                'description' => 'Ban chấp hành Hội Sinh viên Học viện Ngân hàng. Trang thông tin chính thức về các hoạt động, sự kiện của Hội Sinh viên.',
                'type'        => 'public',
                'created_by'  => $admin->id,
            ],
            [
                'name'        => 'Câu lạc bộ Khởi nghiệp HVNH',
                'slug'        => 'clb-khoi-nghiep-hvnh',
                'description' => 'CLB Khởi nghiệp và Đổi mới sáng tạo – nơi kết nối các bạn sinh viên có đam mê kinh doanh.',
                'type'        => 'public',
                'created_by'  => $user1->id,
            ],
            [
                'name'        => 'Trang Dự Án AI',
                'slug'        => 'du-an-ai-hvnh',
                'description' => 'Chia sẻ các dự án AI, Machine Learning của sinh viên Khoa HTTT.',
                'type'        => 'public',
                'created_by'  => $user1->id,
            ],
            [
                'name'        => 'CLB Tiếng Anh BAV',
                'slug'        => 'clb-tieng-anh-bav',
                'description' => 'Banking Academy Vietnam English Club – nâng cao kỹ năng tiếng Anh cho sinh viên.',
                'type'        => 'public',
                'created_by'  => $user2->id,
            ],
            [
                'name'        => 'Khoa Hệ thống Thông tin',
                'slug'        => 'khoa-httt',
                'description' => 'Trang chính thức của Khoa Hệ thống Thông tin – Học viện Ngân hàng.',
                'type'        => 'public',
                'created_by'  => $admin->id,
            ],
        ];

        $createdPages = [];
        foreach ($pages as $p) {
            $page = Page::firstOrCreate(['slug' => $p['slug']], $p);
            $createdPages[] = $page;

            // Auto add creator as admin member
            PageMember::firstOrCreate(
                ['page_id' => $page->id, 'user_id' => $p['created_by']],
                ['role' => 'admin', 'status' => 'approved']
            );
        }

        // ── 4. Follow pages ───────────────────────────────
        $user1->followedPages()->syncWithoutDetaching(
            collect($createdPages)->pluck('id')->toArray()
        );
        $user2->followedPages()->syncWithoutDetaching([$createdPages[0]->id, $createdPages[3]->id]);
        $user3->followedPages()->syncWithoutDetaching([$createdPages[2]->id, $createdPages[4]->id]);

        // ── 5. Posts ──────────────────────────────────────
        $posts = [
            [
                'page_id'    => $createdPages[0]->id,
                'user_id'    => $admin->id,
                'title'      => 'Thông báo: Cuộc thi tìm hiểu Nghị quyết Đại hội Đảng bộ Hà Nội',
                'content'    => "Căn cứ công văn số 3094-CV/TĐTN-CTĐ&TTN của Thành đoàn Hà Nội, BCH Hội Sinh viên triển khai Cuộc thi tìm hiểu Nghị quyết Đại hội đại biểu Đảng bộ thành phố Hà Nội lần thứ XVIII.\n\n📅 Thời gian: 01/03/2026 – 30/03/2026\n📝 Hình thức: Thi trực tuyến\n🎁 Giải thưởng hấp dẫn cho top 10 sinh viên xuất sắc nhất\n\nHãy đăng ký tham gia ngay để thể hiện tinh thần học tập và cống hiến của sinh viên HVNH!",
                'visibility' => 'public',
                'post_type'  => 'post',
                'tags'       => ['thongbao', 'hoisinh vien', 'cuocthi'],
            ],
            [
                'page_id'    => $createdPages[2]->id,
                'user_id'    => $user1->id,
                'title'      => 'Dự án AI nhận diện gian lận tín dụng ngân hàng',
                'content'    => "Nhóm nghiên cứu khoa HTTT vừa hoàn thành dự án ứng dụng Machine Learning để phát hiện gian lận trong giao dịch ngân hàng.\n\n🔬 Công nghệ sử dụng:\n- Python, TensorFlow, Scikit-learn\n- Dataset: 500,000+ giao dịch thực tế\n- Độ chính xác đạt 97.3%\n\nDự án đã được trình bày tại Hội nghị Khoa học Sinh viên 2025 và đạt giải Nhất.",
                'visibility' => 'public',
                'post_type'  => 'project',
                'tags'       => ['AI', 'machinelearning', 'nganHang', 'doAnMonHoc'],
            ],
            [
                'page_id'    => $createdPages[1]->id,
                'user_id'    => $user1->id,
                'title'      => 'Workshop: Khởi nghiệp từ con số 0 – Kinh nghiệm thực chiến',
                'content'    => "CLB Khởi nghiệp HVNH trân trọng kính mời các bạn sinh viên tham gia Workshop đặc biệt!\n\n👨‍💼 Diễn giả: Anh Nguyễn Minh Tuấn – CEO StartupViet\n📅 Thời gian: 15/03/2026, 14:00 – 17:00\n📍 Địa điểm: Hội trường A, Học viện Ngân hàng\n\nNội dung:\n✅ Câu chuyện khởi nghiệp thực tế\n✅ Cách tìm ý tưởng và validate sản phẩm\n✅ Gọi vốn đầu tư – kinh nghiệm từ thực tế\n\nĐăng ký miễn phí tại form dưới đây!",
                'visibility' => 'public',
                'post_type'  => 'post',
                'tags'       => ['workshop', 'khoiNghiep', 'sukien'],
            ],
            [
                'page_id'    => $createdPages[4]->id,
                'user_id'    => $admin->id,
                'title'      => 'Thông báo lịch thi học kỳ 2 năm học 2025-2026',
                'content'    => "Phòng Đào tạo thông báo lịch thi kết thúc học phần học kỳ 2 năm học 2025-2026:\n\n📅 Thời gian thi: 15/04/2026 – 10/05/2026\n📋 Sinh viên xem lịch thi cá nhân trên cổng thông tin sinh viên\n⚠️ Lưu ý: Sinh viên cần mang theo thẻ sinh viên khi dự thi\n\nMọi thắc mắc liên hệ Phòng Đào tạo: daotao@hvnh.edu.vn",
                'visibility' => 'public',
                'post_type'  => 'post',
                'tags'       => ['lichThi', 'thongbao', 'hocKy2'],
            ],
            [
                'page_id'    => null,
                'user_id'    => $user1->id,
                'title'      => 'Chia sẻ kinh nghiệm học Laravel từ đầu',
                'content'    => "Sau 3 tháng tự học Laravel, mình muốn chia sẻ lộ trình học hiệu quả nhất cho các bạn mới bắt đầu:\n\n1️⃣ Học PHP cơ bản (OOP)\n2️⃣ Hiểu MVC pattern\n3️⃣ Laravel cơ bản: Route, Controller, Model, View\n4️⃣ Eloquent ORM & Database\n5️⃣ Authentication & Middleware\n6️⃣ Build project thực tế\n\nTài liệu mình dùng:\n- Laravel Documentation (larave.com)\n- Laracasts.com\n- YouTube: Traversy Media\n\nChúc các bạn học tốt! 🚀",
                'visibility' => 'public',
                'post_type'  => 'achievement',
                'tags'       => ['laravel', 'php', 'lapTrinh', 'hocTap'],
            ],
            [
                'page_id'    => $createdPages[3]->id,
                'user_id'    => $user2->id,
                'title'      => 'English Speaking Club – Buổi sinh hoạt tuần này',
                'content'    => "📢 CLB Tiếng Anh BAV thông báo lịch sinh hoạt tuần này!\n\n🗣️ Topic: \"Digital Banking and Fintech Innovation\"\n📅 Thứ 4, 20/03/2026 – 18:00\n📍 Phòng 201, Tòa nhà B\n\nActivities:\n- Vocabulary building\n- Group discussion\n- Mini debate\n\nWelcome all students! No registration needed 😊",
                'visibility' => 'public',
                'post_type'  => 'post',
                'tags'       => ['english', 'clb', 'tiengAnh'],
            ],
        ];

        foreach ($posts as $p) {
            Post::firstOrCreate(
                ['title' => $p['title'], 'user_id' => $p['user_id']],
                $p
            );
        }

        $this->command->info('✅ Seed dữ liệu mẫu thành công!');
        $this->command->info('👤 Tài khoản test:');
        $this->command->info('   Admin:    admin@hvnh.edu.vn  / Admin@12345!');
        $this->command->info('   User 1:   sv001@hvnh.edu.vn  / SinhVien@123!');
        $this->command->info('   User 2:   sv002@hvnh.edu.vn  / SinhVien@123!');
    }
}