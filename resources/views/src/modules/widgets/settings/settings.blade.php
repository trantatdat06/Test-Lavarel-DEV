<div class="settings-widget-container animate-fade-in-up">
    <div class="widget-header">
        <h3><i class="fa-solid fa-shapes"></i> CÀI ĐẶT HỆ THỐNG</h3>
    </div>

    <div class="settings-group">
        <label>Giao diện</label>
        <div class="setting-item">
            <div class="setting-info">
                <h5>Chế độ tối (Dark Mode)</h5>
                <p>Tiết kiệm pin và bảo vệ mắt</p>
            </div>
            <div class="toggle-switch"><div class="switch-ball"></div></div>
        </div>
    </div>

    <div class="settings-group">
        <label>Dự án AI - Nhóm 6</label>
        <div class="setting-item link">
            <div class="setting-info">
                <h5>Quản lý thành viên</h5>
                <p>Hoài, Duy, Đạt, Đông...</p>
            </div>
            <i class="fa-solid fa-chevron-right"></i>
        </div>
        <div class="setting-item link">
            <div class="setting-info">
                <h5>Thông tin Mentor</h5>
                <p>Giảng viên BAV</p>
            </div>
            <i class="fa-solid fa-chevron-right"></i>
        </div>
    </div>

    <div class="settings-group">
        <label>Tài khoản</label>
        <div class="setting-item link danger">
            <div class="setting-info">
                <h5>Đăng xuất hệ thống</h5>
            </div>
            <i class="fa-solid fa-right-from-bracket"></i>
        </div>
    </div>
</div>

<style>
    .settings-widget-container { padding: 20px; }
    .settings-group { margin-bottom: 25px; }
    .settings-group label { font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 10px; display: block; }
    .setting-item { display: flex; justify-content: space-between; align-items: center; padding: 12px 15px; background: #f8fafc; border-radius: 12px; margin-bottom: 8px; cursor: pointer; transition: all 0.2s; }
    .setting-item:hover { background: #f1f5f9; }
    .setting-info h5 { font-size: 14px; font-weight: 600; color: #1e293b; margin-bottom: 2px; }
    .setting-info p { font-size: 11px; color: #64748b; }
    .toggle-switch { width: 40px; height: 20px; background: #cbd5e1; border-radius: 10px; position: relative; cursor: pointer; }
    .switch-ball { width: 16px; height: 16px; background: #fff; border-radius: 50%; position: absolute; top: 2px; left: 2px; transition: 0.3s; }
    .setting-item.link .fa-chevron-right { font-size: 12px; color: #cbd5e1; }
    .danger { color: #ef4444; }
    .danger .setting-info h5 { color: #ef4444; }
</style>