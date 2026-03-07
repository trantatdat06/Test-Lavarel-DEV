<style>
    /* CSS bám sát Wireframe tab Vai trò */
    .roles-container {
        background: #fff;
        border-radius: 16px;
        padding: 25px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        margin-top: 20px;
    }
    .roles-header {
        font-size: 18px;
        font-weight: 700;
        color: #1c1e21;
        margin-bottom: 20px;
        border-bottom: 1px solid #f0f2f5;
        padding-bottom: 15px;
    }
    
    .role-list {
        display: flex;
        flex-direction: column;
        gap: 15px;
        margin-bottom: 30px;
    }
    .role-item {
        display: flex;
        align-items: center;
        padding: 15px;
        border: 1px solid #e4e6eb;
        border-radius: 12px;
        transition: all 0.2s;
    }
    .role-item:hover {
        background: #fafafa;
        border-color: #ccd0d5;
    }
    
    .role-avatar {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: #4a66f0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 20px;
        color: #fff;
        margin-right: 15px;
        overflow: hidden;
    }
    .role-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .role-info { flex: 1; }
    .role-name { font-size: 16px; font-weight: 700; color: #1c1e21; margin-bottom: 4px; }
    .role-title { font-size: 14px; color: #65676b; }
    .role-title span { font-weight: 600; color: #1877f2; }
    
    .btn-visit {
        background: #e4e6eb;
        color: #050505;
        border: none;
        padding: 8px 20px;
        border-radius: 6px;
        font-weight: 600;
        cursor: pointer;
        transition: 0.2s;
    }
    .btn-visit:hover { background: #d8dadf; }
    
    .roles-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-top: 1px solid #f0f2f5;
        padding-top: 20px;
    }
    .btn-request-page {
        background: #1877f2;
        color: #fff;
        border: none;
        padding: 10px 20px;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        transition: 0.2s;
    }
    .btn-request-page:hover { background: #166fe5; }
    .request-count {
        font-size: 14px;
        color: #65676b;
        font-weight: 600;
        border: 1px solid #ccd0d5;
        padding: 8px 15px;
        border-radius: 8px;
    }
</style>

@php
    // 1. Lấy danh sách các trang mà user này tham gia từ DB
    $pageRoles = \Illuminate\Support\Facades\DB::table('page_members')
        ->join('pages', 'page_members.page_id', '=', 'pages.id')
        ->where('page_members.user_id', $user->id)
        ->where('page_members.status', 'approved') // Chỉ lấy những page đã được duyệt tham gia
        ->select('pages.name', 'pages.slug', 'pages.avatar', 'page_members.role')
        ->get();

    // 2. Từ điển dịch Enum Role sang tiếng Việt cho hiển thị đẹp mắt
    $roleDict = [
        'admin' => 'Quản trị viên (Admin)',
        'content_manager' => 'Quản lý nội dung',
        'member_manager' => 'Quản lý thành viên',
        'info_manager' => 'Quản lý thông tin',
        'system_manager' => 'Quản lý hệ thống'
    ];
@endphp

<div class="roles-container animate-fade-in-up">
    <div class="roles-header">
        Trang đang tham gia ({{ $pageRoles->count() }})
    </div>

    <div class="role-list" id="role-list-container">
        @if($pageRoles->count() > 0)
            @foreach($pageRoles as $item)
                <div class="role-item">
                    <div class="role-avatar">
                        @if($item->avatar)
                            <img src="{{ $item->avatar }}" alt="{{ $item->name }}">
                        @else
                            {{ mb_substr($item->name, 0, 1, 'UTF-8') }}
                        @endif
                    </div>
                    <div class="role-info">
                        <div class="role-name">{{ $item->name }}</div>
                        <div class="role-title">Vai trò: <span>{{ $roleDict[$item->role] ?? $item->role }}</span></div>
                    </div>
                    <button class="btn-visit" onclick="window.location.href='/page/{{ $item->slug }}'">Truy cập</button>
                </div>
            @endforeach
        @else
            <div style="text-align: center; padding: 30px; color: #8e8e8e;">
                <i class="fa-solid fa-users-slash" style="font-size: 40px; margin-bottom: 10px; opacity: 0.5;"></i>
                <p>Bạn chưa tham gia quản trị Trang/CLB nào.</p>
            </div>
        @endif
    </div>

    <div class="roles-footer">
        <button class="btn-request-page" onclick="alert('Form gửi yêu cầu tạo Page sẽ hiện ra ở đây!')">
            <i class="fa-solid fa-plus"></i> Yêu cầu Tạo page
        </button>
        <div class="request-count">Số lượt yêu cầu : <span id="request-count-number">{{ $user->upgrade_attempt_count }}</span></div>
    </div>
</div>