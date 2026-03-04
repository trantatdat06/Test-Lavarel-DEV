<link rel="stylesheet" href="{{ asset('views/src/modules/feed/profile/profile.css') }}">

<div class="profile-container animate-fade-in-up">
    <div class="profile-header-card">
        <div class="cover-image">
            <img id="p-cover" src="https://images.unsplash.com/photo-1557683316-973673baf926?q=80&w=1000" alt="Cover">
            <button class="btn-edit-cover"><i class="fa-solid fa-camera"></i> Đổi ảnh bìa</button>
        </div>

        <div class="header-content">
            <div class="avatar-section">
                <div class="avatar-wrapper">
                    <img id="p-avatar" src="" alt="User Avatar" onerror="this.src='https://ui-avatars.com/api/?name=User&background=random'">
                    <div class="change-avatar"><i class="fa-solid fa-camera"></i></div>
                </div>
            </div>

            <div class="user-meta">
                <div class="name-row">
                    <h2 id="p-name">Đang tải...</h2>
                    <i class="fa-solid fa-circle-check verified-badge" title="Tài khoản sinh viên đã xác thực"></i>
                    <div class="header-btns">
                        <button class="btn-edit-profile">Chỉnh sửa hồ sơ</button>
                        <button class="btn-settings"><i class="fa-solid fa-gear"></i></button>
                    </div>
                </div>
                
                <div class="stats-row">
                    <span><strong id="p-stat-followers">0</strong> Người theo dõi</span>
                    <span><strong id="p-stat-following">0</strong> Đang theo dõi</span>
                </div>
                
                <div class="bio-section">
                    <div class="headline" id="p-role">Sinh viên / Chuyên viên dự án</div>
                    <div class="description" id="p-bio">Sẵn sàng học hỏi và phát triển hệ sinh thái số.</div>
                    <a href="#" class="link" id="p-link">bav.edu.vn/itde</a>
                </div>
            </div>
        </div>

        <div class="profile-tabs" id="profile-tab-menu">
            <div class="tab active" data-tab="all" onclick="switchProfileTab('all')"><i class="fa-solid fa-layer-group"></i> <span class="tab-text">TẤT CẢ</span></div>
            <div class="tab" data-tab="schedule" onclick="switchProfileTab('schedule')"><i class="fa-solid fa-calendar-days"></i> <span class="tab-text">LỊCH TRÌNH</span></div>
            <div class="tab" data-tab="events" onclick="switchProfileTab('events')"><i class="fa-solid fa-calendar-check"></i> <span class="tab-text">SỰ KIỆN HỌC THUẬT</span></div>
            <div class="tab" data-tab="saved" onclick="switchProfileTab('saved')"><i class="fa-solid fa-bookmark"></i> <span class="tab-text">LƯU TRỮ</span></div>
            <div class="tab" data-tab="roles" onclick="switchProfileTab('roles')"><i class="fa-solid fa-user-tie"></i> <span class="tab-text">QUẢN LÝ VAI TRÒ</span></div>
        </div>
    </div>

    <div id="profile-dynamic-content">
        </div>
</div>

<script>
    // Biến lưu trữ dữ liệu người dùng để các file Tab con có thể truy cập
    window.currentProfileUser = {};

    (function initProfile() {
        const userDataStr = sessionStorage.getItem('currentUser');
        const defaultUser = {
            name: "Trần Tất Đạt",
            role: "IT Business Analyst",
            msv: "27A4043284",
            email: "27A4043284@hvnh.edu.vn",
            className: "K27HTTTB",
            bio: "Phát triển dự án Mạng xã hội Học tập tích hợp AI.",
            avatar: "https://ui-avatars.com/api/?name=Đạt&background=4a66f0&color=fff&size=150"
        };

        currentProfileUser = userDataStr ? Object.assign(defaultUser, JSON.parse(userDataStr)) : defaultUser;

        try {
            if(document.getElementById('p-name')) document.getElementById('p-name').innerText = currentProfileUser.name;
            if(document.getElementById('p-role')) document.getElementById('p-role').innerText = currentProfileUser.role;
            if(document.getElementById('p-bio')) document.getElementById('p-bio').innerText = currentProfileUser.bio;
            
            if(document.getElementById('p-stat-followers')) document.getElementById('p-stat-followers').innerText = Math.floor(Math.random() * 500) + 120;
            if(document.getElementById('p-stat-following')) document.getElementById('p-stat-following').innerText = Math.floor(Math.random() * 300) + 50;

            const avatarImg = document.getElementById('p-avatar');
            if (avatarImg && currentProfileUser.avatar) avatarImg.src = currentProfileUser.avatar;
            
            // Tự động kích hoạt tab "TẤT CẢ" khi mới vào Profile
            switchProfileTab('all');
        } catch (e) { console.error("Lỗi đổ dữ liệu Profile:", e); }
    })();

    // Hàm gọi nội dung từ file tab con
    window.switchProfileTab = async function(tabName) {
        const tabContainer = document.getElementById('profile-tab-menu');
        const tabs = tabContainer.querySelectorAll('.tab');
        tabs.forEach(t => t.classList.remove('active'));
        const activeTab = tabContainer.querySelector(`.tab[data-tab="${tabName}"]`);
        if (activeTab) activeTab.classList.add('active');

        const contentBox = document.getElementById('profile-dynamic-content');
        contentBox.style.opacity = '0.5';

        try {
            // Cập nhật đường dẫn fetch khớp với cấu trúc: src/modules/feed/profile/tabs/profile-{tabName}.blade.php
            const response = await fetch(`{{ url('src/modules/feed/profile/tabs/profile-') }}${tabName}.blade.php`);
            if (response.ok) {
                const html = await response.text();
                contentBox.innerHTML = html;
                
                // Kích hoạt các đoạn JS bên trong file Tab con (sử dụng hàm từ index.html)
                if (typeof window.executeDynamicScripts === 'function') {
                    window.executeDynamicScripts(contentBox);
                }
            } else {
                contentBox.innerHTML = `<div style="text-align:center; padding: 50px; color:#8e8e8e; background: #fff; border-radius: 16px; margin-top: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
                    <i class="fa-solid fa-person-digging" style="font-size: 36px; margin-bottom: 15px; color: #dbdbdb;"></i>
                    <h3 style="color: #1c1e21; margin-bottom: 8px;">Đang thi công phân hệ này</h3>
                    <p>Sếp nhớ tạo file <b>profile-${tabName}.blade.php</b> trong thư mục <b>tabs</b> nhé!</p>
                </div>`;
            }
        } catch (error) {
            console.error("Lỗi tải tab:", error);
        } finally {
            contentBox.style.opacity = '1';
            contentBox.style.transition = 'opacity 0.3s ease';
        }
    };
</script>