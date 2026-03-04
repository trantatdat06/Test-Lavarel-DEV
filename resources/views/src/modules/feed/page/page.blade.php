<link rel="stylesheet" href="{{ asset('views/src/modules/feed/page/page.css') }}">

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
                    <i class="fa-solid fa-circle-check verified-badge" title="Tài khoản đã xác thực"></i>
                    <div class="header-btns">
                        <button class="btn-edit-profile">Chỉnh sửa Trang</button>
                        <button class="btn-settings"><i class="fa-solid fa-ellipsis-vertical"></i></button>
                    </div>
                </div>
                <div class="stats-row">
                    <span class="stat"><b>1.2k</b> người theo dõi</span>
                    <span class="stat">Đang theo dõi <b>350</b></span>
                </div>
                <div class="bio-row">
                    <p id="p-bio">Đang tải tiểu sử...</p>
                </div>
            </div>
        </div>

        <div class="profile-tabs" id="profile-tab-menu">
            <div class="tab active" data-tab="posts"><i class="fa-solid fa-table-cells"></i> Bài viết</div>
            <div class="tab" data-tab="all"><i class="fa-solid fa-border-all"></i> Tất cả</div>
            <div class="tab" data-tab="data"><i class="fa-regular fa-address-card"></i> Dữ liệu</div>
            <div class="tab" data-tab="roles"><i class="fa-solid fa-user-shield"></i> Vai trò</div>
            <div class="tab" data-tab="subpages"><i class="fa-solid fa-file-lines"></i> Trang con</div>
        </div>
    </div>

    <div id="profile-dynamic-content" class="tab-content-area">
        </div>
</div>

<script>
    (async function initProfilePage() {
        const user = window.currentProfileUser || { name: 'Người dùng', bio: 'Chưa có tiểu sử', avatar: '' };
        
        document.getElementById('p-name').innerText = user.name;
        document.getElementById('p-bio').innerText = user.bio;
        if(user.avatar) document.getElementById('p-avatar').src = user.avatar;

        // Xử lý chuyển tab
        const tabContainer = document.getElementById('profile-tab-menu');
        if (tabContainer) {
            tabContainer.addEventListener('click', (e) => {
                const tab = e.target.closest('.tab');
                if (tab) {
                    const tabName = tab.getAttribute('data-tab');
                    loadProfileTab(tabName);
                }
            });
        }

        // Mặc định load tab bài viết
        loadProfileTab('posts');
    })();

    async function loadProfileTab(tabName) {
        const tabContainer = document.getElementById('profile-tab-menu');
        const tabs = tabContainer.querySelectorAll('.tab');
        tabs.forEach(t => t.classList.remove('active'));
        const activeTab = tabContainer.querySelector(`.tab[data-tab="${tabName}"]`);
        if (activeTab) activeTab.classList.add('active');

        const contentBox = document.getElementById('profile-dynamic-content');
        contentBox.style.opacity = '0.5';

        try {
            // Sửa đường dẫn fetch khớp với cấu trúc: src/modules/feed/page/tabs/page-{tabName}.blade.php
            const response = await fetch(`{{ url('src/modules/feed/page/tabs/page-') }}${tabName}.blade.php`);
            if (response.ok) {
                const html = await response.text();
                contentBox.innerHTML = html;
                if (typeof window.executeDynamicScripts === 'function') {
                    window.executeDynamicScripts(contentBox);
                }
            } else {
                contentBox.innerHTML = `<div style="text-align:center; padding: 50px; color:#8e8e8e; background: #fff; border-radius: 16px; margin-top: 20px;">
                    <i class="fa-solid fa-file-circle-plus" style="font-size: 36px; margin-bottom: 15px;"></i>
                    <h3>Tab ${tabName} đang được xây dựng</h3>
                </div>`;
            }
        } catch (error) {
            console.error("Lỗi tải tab:", error);
        } finally {
            contentBox.style.opacity = '1';
        }
    }
</script>