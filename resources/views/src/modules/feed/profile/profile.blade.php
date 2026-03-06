<link rel="stylesheet" href="{{ asset('views/src/modules/feed/profile/profile.css') }}">

<div class="profile-container animate-fade-in-up">
    <div class="profile-header-card">
        
        <div class="cover-image" style="position: relative;">
            <img id="p-cover" src="https://images.unsplash.com/photo-1557683316-973673baf926?q=80&w=1000" alt="Cover" style="width: 100%; height: 100%; object-fit: cover;">
            <label for="upload-cover-input" style="position: absolute; bottom: 15px; right: 20px; background: rgba(0,0,0,0.6); color: white; padding: 8px 16px; border-radius: 8px; cursor: pointer; backdrop-filter: blur(4px); font-size: 14px; font-weight: 600; transition: 0.2s; z-index: 10;">
                <i class="fa-solid fa-camera"></i> Đổi ảnh bìa
            </label>
        </div>

        <div class="header-content">
            <div class="avatar-section">
                <div class="avatar-wrapper" style="position: relative; width: 150px; height: 150px; border-radius: 50%; border: 6px solid #fff; box-shadow: 0 4px 10px rgba(0,0,0,0.1); background: #eee;">
                    <img id="p-avatar" src="" alt="User Avatar" onerror="this.src='https://ui-avatars.com/api/?name=User&background=random'" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                    <label for="upload-avatar-input" style="position: absolute; bottom: 5px; right: 5px; background: #e4e6eb; color: #1c1e21; width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; box-shadow: 0 2px 5px rgba(0,0,0,0.2); transition: 0.2s; border: 2px solid #fff; z-index: 10;">
                        <i class="fa-solid fa-camera" style="font-size: 16px;"></i>
                    </label>
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
                
                <div class="bio-section">
                    <div class="description" id="p-bio">Đang tải tiểu sử...</div>
                    <a href="#" class="link" id="p-link" style="display: none;"></a>
                </div>
            </div>
        </div>

        <div class="profile-tabs" id="profile-tab-menu">
            <div class="tab active" data-tab="info" onclick="window.switchProfileTab('info')"><i class="fa-solid fa-address-card"></i> <span class="tab-text">THÔNG TIN</span></div>
            <div class="tab" data-tab="schedule" onclick="window.switchProfileTab('schedule')"><i class="fa-solid fa-calendar-days"></i> <span class="tab-text">LỊCH TRÌNH</span></div>
            <div class="tab" data-tab="saved" onclick="window.switchProfileTab('saved')"><i class="fa-solid fa-bookmark"></i> <span class="tab-text">LƯU TRỮ</span></div>
            <div class="tab" data-tab="roles" onclick="window.switchProfileTab('roles')"><i class="fa-solid fa-user-tie"></i> <span class="tab-text">QUẢN LÝ VAI TRÒ</span></div>
        </div>
    </div>

    <input type="file" id="upload-avatar-input" accept="image/png, image/jpeg, image/jpg" style="display: none;" onchange="window.handleImageUpload(this, 'avatar')">
    <input type="file" id="upload-cover-input" accept="image/png, image/jpeg, image/jpg" style="display: none;" onchange="window.handleImageUpload(this, 'cover')">

    <div id="profile-dynamic-content"></div>
</div>

<script>
    // 1. LẤY DỮ LIỆU TỪ PHP CONTROLLER
    let userDB = {!! isset($profileData) ? $profileData : '{}' !!};
    if (typeof userDB === 'string' && userDB !== '{}') {
        try { userDB = JSON.parse(userDB); } catch(e) {}
    }

    window.currentProfileUser = (Object.keys(userDB).length > 0) ? userDB : {
        name: "Người dùng hệ thống", msv: "SV001", bio: "Chưa có tiểu sử",
        avatar: "https://ui-avatars.com/api/?name=User&background=4a66f0&color=fff&size=150",
        cover: "https://images.unsplash.com/photo-1557683316-973673baf926?q=80&w=1000"
    };

    // =========================================================================
    // HÀM ÉP TRÌNH DUYỆT CHẠY SCRIPT (Fix lỗi đóng băng JS)
    // =========================================================================
    window.executeDynamicScripts = function(container) {
        const scripts = container.querySelectorAll('script');
        scripts.forEach(oldScript => {
            const newScript = document.createElement('script');
            newScript.textContent = oldScript.textContent;
            // Gắn thẳng vào thẻ <body> để trình duyệt bắt buộc phải chạy
            document.body.appendChild(newScript);
            oldScript.remove(); // Dọn rác
        });
    };

    // Hàm chuyển Tab (Đã tích hợp mã chống Cache `?v=...`)
    window.switchProfileTab = async function(tabName) {
        const tabContainer = document.getElementById('profile-tab-menu');
        const tabs = tabContainer.querySelectorAll('.tab');
        tabs.forEach(t => t.classList.remove('active'));
        
        const activeTab = tabContainer.querySelector(`.tab[data-tab="${tabName}"]`);
        if (activeTab) activeTab.classList.add('active');

        const contentBox = document.getElementById('profile-dynamic-content');
        contentBox.style.opacity = '0.5';

        try {
            // FIX: Gắn thêm timestamp để đánh lừa trình duyệt, bắt nó tải file mới nhất!
            const noCacheUrl = `{{ url('src/modules/feed/profile/tabs/profile-') }}${tabName}.blade.php?v=` + new Date().getTime();
            
            const response = await fetch(noCacheUrl);
            if (response.ok) {
                contentBox.innerHTML = await response.text();
                // Kích hoạt Script
                window.executeDynamicScripts(contentBox);
            }
        } catch (error) {
            console.error("Lỗi tải tab:", error);
        } finally {
            contentBox.style.opacity = '1';
        }
    };

    // Hàm Upload Ảnh
    window.handleImageUpload = async function(inputElement, type) {
        const file = inputElement.files[0];
        if (!file) return;

        const studentCode = window.currentProfileUser.msv;
        const formData = new FormData();
        formData.append('image', file);
        formData.append('type', type);

        const imgElement = type === 'avatar' ? document.getElementById('p-avatar') : document.getElementById('p-cover');
        imgElement.style.opacity = '0.5';

        try {
            const response = await fetch(`/profile/${studentCode}/upload-image`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: formData
            });

            const result = await response.json();

            if (response.ok) {
                imgElement.src = result.url;
                window.currentProfileUser[type] = result.url;
            } else {
                alert("Hệ thống từ chối: " + (result.message || "Ảnh không hợp lệ."));
            }
        } catch (error) {
            console.error("Upload error:", error);
            alert("Lỗi kết nối máy chủ PHP!");
        } finally {
            imgElement.style.opacity = '1';
            inputElement.value = ""; 
        }
    };

    // Khởi tạo giao diện
    function initProfile() {
        document.getElementById('p-name').innerText = window.currentProfileUser.name;
        document.getElementById('p-bio').innerText = window.currentProfileUser.bio || "Chưa có tiểu sử";
        
        const linkEl = document.getElementById('p-link');
        if(linkEl && window.currentProfileUser.social_links) {
            linkEl.innerText = window.currentProfileUser.social_links;
            linkEl.href = window.currentProfileUser.social_links.startsWith('http') ? window.currentProfileUser.social_links : 'https://' + window.currentProfileUser.social_links;
            linkEl.style.display = 'block';
        }

        if (window.currentProfileUser.avatar) document.getElementById('p-avatar').src = window.currentProfileUser.avatar;
        if (window.currentProfileUser.cover) document.getElementById('p-cover').src = window.currentProfileUser.cover;
        
        // Mở sẵn Tab Thông tin
        window.switchProfileTab('info');
    }

    initProfile();
</script>