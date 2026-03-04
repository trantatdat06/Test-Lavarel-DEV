<link rel="stylesheet" href="{{ asset('views/src/components/header/header.css') }}">

<div class="header-inner">
    <div class="header-col-1">
        <div class="icon-btn" id="sidebar-toggle" title="Menu"><i class="fa-solid fa-bars"></i></div>
    </div>

    <div class="header-col-2">
        <div class="logo-area">Instagram</div>
        <div class="center-content">
            <div class="header-search">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" placeholder="Tìm tài liệu, lớp học...">
            </div>
            
            <div class="action-group main-nav" id="mobile-main-nav">
                <div class="icon-btn active" data-column="col-feed" data-type="feed" data-module="home" title="Trang chủ">
                    <i class="fa-solid fa-house"></i>
                </div>
                <div class="icon-btn" data-column="col-feed" data-type="feed" data-module="favorite" title="Yêu thích">
                    <i class="fa-solid fa-heart"></i>
                </div>
                <div class="icon-btn" data-column="col-feed" data-type="feed" data-module="explore" title="Khám phá AI">
                    <i class="fa-solid fa-compass"></i>
                </div>
                <div class="icon-btn" data-column="col-feed" data-type="feed" data-module="mixed" title="Hỗn hợp">
                    <i class="fa-solid fa-earth-americas"></i>
                </div>
                
                <div class="icon-btn mob-sidebar-btn" id="mobile-sidebar-toggle" title="Menu"><i class="fa-solid fa-bars"></i></div>
                
                <div class="icon-btn mob-widget-btn" id="mobile-widget-toggle" onclick="window._mobileWidgetToggle()" title="Widget"><i class="fa-solid fa-table-cells"></i></div>
            </div>
            <div class="action-group widget-nav mobile-widget-row" id="mobile-widget-row">
                <div class="icon-btn" data-column="col-widget" data-type="widgets" data-module="settings" title="Quản lý Module"><i class="fa-solid fa-shapes"></i></div>
                <div class="icon-btn" data-column="col-widget" data-type="widgets" data-module="todo" title="Nhiệm vụ"><i class="fa-solid fa-clipboard-check"></i></div>
                <div class="icon-btn" data-column="col-widget" data-type="widgets" data-module="events" title="Sự kiện"><i class="fa-solid fa-calendar-check"></i></div>
                <div class="icon-btn" data-column="col-widget" data-type="widgets" data-module="calendar" title="Lịch học"><i class="fa-solid fa-calendar-days"></i></div>
                <div class="icon-btn" data-column="col-widget" data-type="widgets" data-module="notifications" title="Thông báo"><i class="fa-solid fa-bell"></i></div>
                <div class="icon-btn mob-back-btn" id="mobile-widget-back" onclick="window._mobileWidgetBack()" title="Quay lại"><i class="fa-solid fa-arrow-left"></i></div>
            </div>
        </div>
    </div>

    <div class="header-col-3">
        <div class="action-group widget-nav">
            <div class="icon-btn" data-column="col-widget" data-type="widgets" data-module="settings" title="Quản lý Module">
                <i class="fa-solid fa-shapes"></i>
            </div>
            <div class="icon-btn" data-column="col-widget" data-type="widgets" data-module="todo" title="Nhiệm vụ Nhóm 6">
                <i class="fa-solid fa-clipboard-check"></i>
            </div>
            <div class="icon-btn" data-column="col-widget" data-type="widgets" data-module="events" title="Sự kiện học thuật">
                <i class="fa-solid fa-calendar-check"></i>
            </div>
            <div class="icon-btn" data-column="col-widget" data-type="widgets" data-module="calendar" title="Lịch học & Lịch trình">
                <i class="fa-solid fa-calendar-days"></i>
            </div>
            <div class="icon-btn" data-column="col-widget" data-type="widgets" data-module="notifications" title="Thông báo">
                <i class="fa-solid fa-bell"></i>
            </div>
        </div>
    </div>

    <div class="header-col-4">
        <div class="user-avatar-container" id="avatar-container">
            <div class="user-avatar">
                <img id="header-avatar-img" src="https://via.placeholder.com/36" alt="User">
            </div>
            <div class="avatar-dropdown" id="avatar-dropdown">
                <div class="dropdown-item" data-column="col-feed" data-type="feed" data-module="profile">
                    <i class="fa-solid fa-circle-user"></i> <span id="header-display-name">Hồ sơ cá nhân</span>
                </div>
                <hr>
                <div class="dropdown-item logout" onclick="sessionStorage.clear(); window.location.href='{{ url('/auth') }}';">
                    <i class="fa-solid fa-right-from-bracket"></i> Đăng xuất
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Update avatar/name
    (function() {
        var userData = sessionStorage.getItem('currentUser');
        if (userData) {
            var user = JSON.parse(userData);
            var avatarImg = document.getElementById('header-avatar-img');
            var nameSpan  = document.getElementById('header-display-name');
            if (avatarImg) avatarImg.src = user.avatar;
            if (nameSpan)  nameSpan.innerText = "Hồ sơ của " + user.name.split(' ').pop();
        }
    })();

    window._mobileWidgetToggle = function() {
        if (window.innerWidth >= 720) return;
        var mn = document.getElementById('mobile-main-nav');
        var wr = document.getElementById('mobile-widget-row');
        if (!mn || !wr) return;
        mn.style.display = 'none';
        wr.style.display = 'flex';
    };
    
    window._mobileWidgetBack = function() {
        if (window.innerWidth >= 720) return;
        var mn = document.getElementById('mobile-main-nav');
        var wr = document.getElementById('mobile-widget-row');
        if (!mn || !wr) return;
        wr.style.display = 'none';
        mn.style.display = 'flex';
    };
</script>