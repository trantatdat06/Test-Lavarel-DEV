<style>
    /* ÉP FONT CHỮ HIỆN ĐẠI CHO TOÀN BỘ KHỐI VÀ FIX LỆCH LAYOUT */
    .roles-container {
        font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
        background: #fff;
        border-radius: 16px;
        padding: 25px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        margin-top: 20px;
    }
    
    .roles-container * {
        box-sizing: border-box;
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
        flex-shrink: 0;
    }
    .role-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .role-info { 
        flex: 1; 
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
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
        font-size: 14px;
        cursor: pointer;
        transition: 0.2s;
        font-family: inherit;
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
        font-size: 14px;
        cursor: pointer;
        transition: 0.2s;
        font-family: inherit;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    .btn-request-page:hover { background: #166fe5; }
    
    .request-count {
        font-size: 14px;
        color: #65676b;
        font-weight: 600;
        border: 1px solid #ccd0d5;
        padding: 8px 15px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    /* --- CSS CHO FORM YÊU CẦU --- */
    .form-wrapper { padding: 5px 0; }
    .form-group { margin-bottom: 20px; }
    .form-label { 
        display: block; 
        font-weight: 600; 
        font-size: 14px; 
        color: #1c1e21; 
        margin-bottom: 8px; 
    }
    .form-input, .form-select, .form-textarea { 
        width: 100%; 
        padding: 12px 15px; 
        border: 1px solid #ccd0d5; 
        border-radius: 8px; 
        font-family: inherit; 
        font-size: 14px; 
        outline: none; 
        transition: all 0.2s ease; 
        background: #fff; 
        color: #1c1e21;
    }
    .form-input:focus, .form-select:focus, .form-textarea:focus { 
        border-color: #1877f2; 
        box-shadow: 0 0 0 2px rgba(24, 119, 242, 0.2); 
    }
    .form-input::placeholder, .form-textarea::placeholder { color: #8e8e8e; }
    
    .btn-cancel { 
        background: #e4e6eb; 
        color: #050505; 
        border: none; 
        padding: 10px 20px; 
        border-radius: 8px; 
        font-weight: 600; 
        font-size: 14px;
        cursor: pointer; 
        transition: 0.2s; 
        font-family: inherit;
    }
    .btn-cancel:hover { background: #d8dadf; }
    
    .form-header-box {
        display: flex;
        align-items: center;
        gap: 15px;
        border-bottom: 1px solid #f0f2f5;
        padding-bottom: 20px;
        margin-bottom: 25px;
    }
    .form-header-icon {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: #e7f3ff;
        color: #1877f2;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        flex-shrink: 0;
    }
    .form-header-text h3 {
        font-size: 18px;
        font-weight: 700;
        color: #1c1e21;
        margin: 0 0 4px 0;
    }
    .form-header-text p {
        font-size: 13px;
        color: #65676b;
        font-weight: 500;
        margin: 0;
    }
</style>

@php
    $pageRoles = \Illuminate\Support\Facades\DB::table('page_members')
        ->join('pages', 'page_members.page_id', '=', 'pages.id')
        ->where('page_members.user_id', $user->id)
        ->where('page_members.status', 'approved')
        ->select('pages.name', 'pages.slug', 'pages.avatar', 'page_members.role')
        ->get();

    // TÍNH TOÁN SỐ LƯỢT CÒN LẠI (Tối đa 3)
    $remainingRequests = 3 - $user->upgrade_attempt_count;
    if ($remainingRequests < 0) $remainingRequests = 0;

    $roleDict = [
        'admin' => 'Quản trị viên (Admin)',
        'content_manager' => 'Quản lý nội dung',
        'member_manager' => 'Quản lý thành viên',
        'info_manager' => 'Quản lý thông tin',
        'system_manager' => 'Quản lý hệ thống'
    ];
@endphp

<div class="roles-container animate-fade-in-up">
    <div id="roles-default-view">
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
                <div style="text-align: center; padding: 40px; color: #8e8e8e;">
                    <i class="fa-solid fa-users-slash" style="font-size: 40px; margin-bottom: 15px; opacity: 0.5;"></i>
                    <p style="margin: 0; font-weight: 500;">Bạn chưa tham gia quản trị Trang/CLB nào.</p>
                </div>
            @endif
        </div>

        <div class="roles-footer">
            @if($remainingRequests > 0)
                <button class="btn-request-page" onclick="window.toggleRoleForm(true)">
                    <i class="fa-solid fa-plus"></i> Yêu cầu Tạo page
                </button>
            @else
                <button class="btn-request-page" style="background: #ccd0d5; color: #65676b; cursor: not-allowed;" disabled title="Bạn đã hết lượt yêu cầu">
                    <i class="fa-solid fa-lock"></i> Đã khóa tính năng
                </button>
            @endif
            
            <div class="request-count">
                Số lượt yêu cầu còn lại: 
                <span id="request-count-number" style="color: {{ $remainingRequests > 0 ? '#23a559' : '#dc3545' }}; font-weight: 800; font-size: 15px;">
                    {{ $remainingRequests }}/3
                </span>
            </div>
        </div>
    </div>

    <div id="roles-request-form" style="display: none;" class="form-wrapper">
        <div class="form-header-box">
            <div class="form-header-icon">
                <i class="fa-solid fa-file-pen"></i>
            </div>
            <div class="form-header-text">
                <h3>Đơn yêu cầu thành lập Trang/CLB mới</h3>
                <p>Vui lòng điền đầy đủ thông tin để Ban quản trị hệ thống xét duyệt</p>
            </div>
        </div>
        
        <div class="form-group">
            <label class="form-label">Tên Trang / CLB <span style="color: #dc3545;">*</span></label>
            <input type="text" id="req-page-name" class="form-input" placeholder="Nhập tên trang chính thức...">
        </div>
        
        <div class="form-group">
            <label class="form-label">Phân loại <span style="color: #dc3545;">*</span></label>
            <select id="req-page-category" class="form-select">
                <option value="club">Câu lạc bộ / Đội / Nhóm</option>
                <option value="faculty">Trang trực thuộc Khoa</option>
                <option value="student_union">Hội Sinh viên</option>
                <option value="youth_union">Đoàn Thanh niên</option>
                <option value="other">Khác</option>
            </select>
        </div>
        
        <div class="form-group">
            <label class="form-label">Mục đích / Lý do thành lập <span style="color: #dc3545;">*</span></label>
            <textarea id="req-page-desc" class="form-textarea" rows="4" placeholder="Trình bày rõ mục đích, kế hoạch hoạt động để hệ thống xét duyệt..."></textarea>
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 30px; border-top: 1px solid #f0f2f5; padding-top: 20px;">
            <button class="btn-cancel" onclick="window.toggleRoleForm(false)">Hủy bỏ</button>
            <button class="btn-request-page" onclick="window.submitPageRequest(this)">Gửi yêu cầu xét duyệt</button>
        </div>
    </div>
</div>

<script>
    window.toggleRoleForm = function(showForm) {
        document.getElementById('roles-default-view').style.display = showForm ? 'none' : 'block';
        document.getElementById('roles-request-form').style.display = showForm ? 'block' : 'none';
    };

    window.submitPageRequest = async function(btn) {
        const name = document.getElementById('req-page-name').value.trim();
        const category = document.getElementById('req-page-category').value;
        const desc = document.getElementById('req-page-desc').value.trim();

        if(!name || !desc) {
            alert("Vui lòng điền đầy đủ Tên trang và Lý do thành lập!");
            return;
        }

        btn.disabled = true;
        btn.innerText = "Đang gửi...";

        try {
            const response = await fetch(`/profile/${window.studentCode}/request-page`, {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/json', 
                    'X-CSRF-TOKEN': '{{ csrf_token() }}' 
                },
                body: JSON.stringify({ name, category, description: desc })
            });

            if(response.ok) {
                alert("Đã gửi yêu cầu thành công! Vui lòng chờ Ban quản trị hệ thống phê duyệt.");
                window.switchProfileTab('roles'); // Tải lại tab để cập nhật UI
            } else {
                const data = await response.json(); 
                alert(data.message || "Lỗi! Không thể gửi yêu cầu.");
                btn.disabled = false;
                btn.innerText = "Gửi yêu cầu xét duyệt";
            }
        } catch(e) {
            alert("Lỗi kết nối máy chủ!");
            btn.disabled = false;
            btn.innerText = "Gửi yêu cầu xét duyệt";
        }
    };
</script>