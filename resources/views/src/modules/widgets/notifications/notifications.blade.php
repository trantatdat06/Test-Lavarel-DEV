<style>
    .notis-container {
        background-color: #1e1f22; /* Màu nền Dark của Discord */
        color: #ffffff;
        padding: 20px;
        height: 100%;
        animation: fadeIn 0.3s ease;
    }
    .notis-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }
    .notis-header h3 { font-size: 1.2rem; font-weight: 700; }
    
    .noti-item {
        display: flex;
        gap: 12px;
        padding: 12px;
        border-radius: 10px;
        transition: background 0.2s;
        cursor: pointer;
        margin-bottom: 8px;
        position: relative;
    }
    .noti-item:hover { background-color: #2b2d31; }
    .noti-item.unread { background-color: rgba(88, 101, 242, 0.1); }
    .noti-item.unread::after {
        content: '';
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        width: 8px;
        height: 8px;
        background-color: #5865f2;
        border-radius: 50%;
    }

    .noti-avatar { width: 40px; height: 40px; border-radius: 50%; overflow: hidden; flex-shrink: 0; }
    .noti-avatar img { width: 100%; height: 100%; object-fit: cover; }
    
    .noti-content p { font-size: 0.85rem; margin: 0; line-height: 1.4; color: #dbdee1; }
    .noti-content strong { color: #ffffff; }
    .noti-time { font-size: 0.75rem; color: #949ba4; margin-top: 4px; display: block; }
</style>

<div class="notis-container">
    <div class="notis-header">
        <h3>Thông báo</h3>
        <i class="fa-solid fa-ellipsis" style="color: #b5bac1; cursor: pointer;"></i>
    </div>

    <div class="noti-item unread">
        <div class="noti-avatar"><img src="https://via.placeholder.com/40" alt="Hoài"></div>
        <div class="noti-content">
            <p><strong>Hoài</strong> đã hoàn thành bản vẽ UI cho Module Explore. Kiểm tra ngay sếp ơi!</p>
            <span class="noti-time">5 phút trước</span>
        </div>
    </div>

    <div class="noti-item">
        <div class="noti-avatar"><img src="https://via.placeholder.com/40" alt="Duy"></div>
        <div class="noti-content">
            <p><strong>Duy</strong> vừa cập nhật cấu trúc Database cho phần quản lý sự kiện BAV.</p>
            <span class="noti-time">1 giờ trước</span>
        </div>
    </div>

    <div class="noti-item unread">
        <div class="noti-avatar"><img src="https://via.placeholder.com/40" alt="Đông"></div>
        <div class="noti-content">
            <p><strong>Đông</strong> đã tích hợp xong Prompt AI cho bộ lọc tin tức tự động.</p>
            <span class="noti-time">3 giờ trước</span>
        </div>
    </div>

    <div class="noti-item">
        <div class="noti-avatar"><img src="https://via.placeholder.com/40" alt="System"></div>
        <div class="noti-content">
            <p><strong>Hệ thống:</strong> Bạn có lịch họp với mentor <strong>Giang Thị Thu Huyền</strong> vào sáng mai.</p>
            <span class="noti-time">6 giờ trước</span>
        </div>
    </div>
</div>