<link rel="stylesheet" href="{{ asset('views/src/components/widgets/right-widget.css') }}">

<div class="widgets-container">
    <div class="widget-card notification-widget">
        <div class="widget-header">
            <h3>Thông báo</h3>
            <div class="header-actions">
                <span class="tab-item active">Tất cả</span>
                <span class="tab-item">Chưa đọc</span>
                <i class="fa-solid fa-ellipsis"></i>
            </div>
        </div>
        
        <div class="widget-content">
            <div class="section-label">Trước đó <a href="#">Xem tất cả</a></div>
            
            <div class="noti-item unread">
                <div class="noti-avatar">
                    <img src="https://via.placeholder.com/40" alt="Avatar">
                    <div class="noti-type-icon blue"><i class="fa-solid fa-at"></i></div>
                </div>
                <div class="noti-text">
                    <p><strong>Hoài</strong> đã nhắc đến bạn trong một bình luận tại module <b>Đăng ký người dùng</b>.</p>
                    <span class="time">10 phút trước</span>
                </div>
                <div class="unread-dot"></div>
            </div>

            <div class="noti-item">
                <div class="noti-avatar">
                    <img src="https://via.placeholder.com/40" alt="Avatar">
                    <div class="noti-type-icon green"><i class="fa-solid fa-code-branch"></i></div>
                </div>
                <div class="noti-text">
                    <p><strong>Duy</strong> vừa cập nhật dữ liệu API cho phần tích hợp AI.</p>
                    <span class="time">2 giờ trước</span>
                </div>
            </div>

            <div class="noti-item unread">
                <div class="noti-avatar">
                    <img src="https://via.placeholder.com/40" alt="Avatar">
                    <div class="noti-type-icon purple"><i class="fa-solid fa-robot"></i></div>
                </div>
                <div class="noti-text">
                    <p><strong>Đông</strong> đã hoàn thành bài kiểm tra chất lượng giọng nói cho mẫu Rap Melodic.</p>
                    <span class="time">5 giờ trước</span>
                </div>
                <div class="unread-dot"></div>
            </div>

            <div class="noti-item">
                <div class="noti-avatar">
                    <img src="https://via.placeholder.com/40" alt="System">
                    <div class="noti-type-icon red"><i class="fa-solid fa-calendar-check"></i></div>
                </div>
                <div class="noti-text">
                    <p><b>Nhắc nhở:</b> Chỉ còn 40 ngày đến hạn nộp sản phẩm cuối cùng (31/03/2026).</p>
                    <span class="time">Hôm nay</span>
                </div>
            </div>
        </div>
        
        <button class="btn-show-more">Xem thông báo trước đó</button>
    </div>
</div>