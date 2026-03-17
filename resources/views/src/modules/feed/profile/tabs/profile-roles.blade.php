<div class="roles-container animate-fade-in-up" style="padding: 25px; background: #fff; border-radius: 16px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); font-family: system-ui, sans-serif;">
    @php
        // Đảm bảo lấy đúng User và danh sách trang đã duyệt
        $studentCode = request()->segment(2);
        $user = DB::table('users')->where('student_code', $studentCode)->first();
        
        $pageRoles = [];
        $remaining = 0;

        if ($user) {
            $pageRoles = DB::table('page_members')
                ->join('pages', 'page_members.page_id', '=', 'pages.id')
                ->where('page_members.user_id', $user->id)
                ->where('page_members.status', 'approved')
                ->select('pages.name', 'pages.slug', 'page_members.role')
                ->get();
            $remaining = $user->upgrade_attempt_count ?? 0;
        }
        
        $roleDict = ['admin' => 'Quản trị viên', 'content_manager' => 'Quản lý nội dung'];
    @endphp

    <div id="roles-default-view">
        <h3 style="margin-top: 0; font-size: 18px; color: #1c1e21;">Trang đang tham gia ({{ count($pageRoles) }})</h3>
        
        <div style="display: flex; flex-direction: column; gap: 12px; margin-bottom: 25px;">
            @forelse($pageRoles as $item)
                <div style="display: flex; align-items: center; padding: 15px; border: 1px solid #e4e6eb; border-radius: 12px;">
                    <div style="flex: 1;">
                        <div style="font-weight: 700; color: #1c1e21;">{{ $item->name }}</div>
                        <div style="font-size: 13px; color: #65676b;">Vai trò: <span style="color: #1877f2; font-weight: 600;">{{ $roleDict[$item->role] ?? $item->role }}</span></div>
                    </div>
                    <button onclick="window.location.href='/page/{{ $item->slug }}'" style="background: #e4e6eb; border: none; padding: 8px 15px; border-radius: 6px; font-weight: 600; cursor: pointer;">Truy cập</button>
                </div>
            @empty
                <p style="text-align: center; color: #8e8e8e; padding: 30px;">Bạn chưa tham gia quản trị trang nào.</p>
            @endforelse
        </div>

        <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid #f0f2f5; padding-top: 20px;">
            @if($remaining > 0)
                <button onclick="document.getElementById('roles-default-view').style.display='none'; document.getElementById('roles-request-form').style.display='block';" style="background: #1877f2; color: #fff; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 600; cursor: pointer;">+ Tạo trang mới</button>
            @else
                <button style="background: #ccd0d5; color: #65676b; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 600; cursor: not-allowed;" disabled>Đã khóa tính năng</button>
            @endif
            
            <div style="font-size: 14px; color: #65676b; font-weight: 600;">Số lượt còn lại: <span style="color: {{ $remaining > 0 ? '#23a559' : '#dc3545' }}; font-weight: 800;">{{ $remaining }}/3</span></div>
        </div>
    </div>

    <div id="roles-request-form" style="display: none;">
        <h3 style="margin-top: 0; font-size: 18px;">Đơn yêu cầu tạo Trang mới</h3>
        <div style="margin-bottom: 15px;">
            <label style="display: block; font-weight: 600; margin-bottom: 5px; font-size: 14px;">Tên Trang / CLB</label>
            <input type="text" id="req_p_name" style="width: 100%; padding: 10px; border: 1px solid #ccd0d5; border-radius: 8px; outline: none;">
        </div>
        <div style="margin-bottom: 15px;">
            <label style="display: block; font-weight: 600; margin-bottom: 5px; font-size: 14px;">Phân loại</label>
            <select id="req_p_cat" style="width: 100%; padding: 10px; border: 1px solid #ccd0d5; border-radius: 8px;">
                <option value="club">Câu lạc bộ / Đội / Nhóm</option>
                <option value="faculty">Trang trực thuộc Khoa</option>
            </select>
        </div>
        <div style="margin-bottom: 15px;">
            <label style="display: block; font-weight: 600; margin-bottom: 5px; font-size: 14px;">Lý do thành lập</label>
            <textarea id="req_p_desc" rows="4" style="width: 100%; padding: 10px; border: 1px solid #ccd0d5; border-radius: 8px; outline: none;"></textarea>
        </div>
        <div style="display: flex; justify-content: flex-end; gap: 10px;">
            <button onclick="document.getElementById('roles-request-form').style.display='none'; document.getElementById('roles-default-view').style.display='block';" style="background: #e4e6eb; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 600; cursor: pointer;">Hủy bỏ</button>
            <button onclick="sendRoleRequest(this)" style="background: #1877f2; color: #fff; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 600; cursor: pointer;">Gửi yêu cầu</button>
        </div>
    </div>
</div>

<script>
    async function sendRoleRequest(btn) {
        const name = document.getElementById('req_p_name').value.trim();
        const category = document.getElementById('req_p_cat').value;
        const description = document.getElementById('req_p_desc').value.trim();

        if(!name || !description) return alert("Vui lòng điền đủ thông tin!");
        
        btn.disabled = true; btn.innerText = "Đang gửi...";
        try {
            const response = await fetch(`/profile/{{ $studentCode }}/request-page`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ name, category, description })
            });
            if(response.ok) {
                alert("Gửi yêu cầu thành công!");
                window.location.reload();
            } else {
                const data = await response.json();
                alert(data.message || "Lỗi xử lý!");
                btn.disabled = false; btn.innerText = "Gửi yêu cầu";
            }
        } catch(e) { alert("Lỗi kết nối máy chủ!"); btn.disabled = false; }
    }
</script>