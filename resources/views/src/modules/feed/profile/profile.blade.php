<link rel="stylesheet" href="{{ asset('views/src/modules/feed/profile/profile.css') }}">

<div class="profile-container animate-fade-in-up">
    <div class="profile-header-card">
        
        <div class="cover-image" style="position: relative;">
            <img id="p-cover" 
                 src="{{ $user->cover ?? 'https://images.unsplash.com/photo-1557683316-973673baf926?q=80&w=1000' }}" 
                 alt="Cover" 
                 style="width: 100%; height: 100%; object-fit: cover;">
            <label for="upload-cover-input" class="btn-edit-cover" style="position: absolute; bottom: 15px; right: 20px; background: rgba(0,0,0,0.6); color: white; border: none; padding: 8px 16px; border-radius: 8px; font-weight: 600; cursor: pointer; backdrop-filter: blur(4px); z-index: 10;">
                <i class="fa-solid fa-camera"></i> Đổi ảnh bìa
            </label>
        </div>

        <div class="header-content">
            <div class="avatar-section">
                <div class="avatar-wrapper" style="position: relative; width: 150px; height: 150px; border-radius: 50%; border: 6px solid #fff; box-shadow: 0 4px 10px rgba(0,0,0,0.1); background: #eee;">
                    <img id="p-avatar" 
                         src="{{ $user->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($user->full_name).'&background=4a66f0&color=fff' }}" 
                         alt="User Avatar" 
                         style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                    <label for="upload-avatar-input" style="position: absolute; bottom: 5px; right: 5px; background: #e4e6eb; color: #1c1e21; width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; box-shadow: 0 2px 5px rgba(0,0,0,0.2); transition: 0.2s; border: 2px solid #fff; z-index: 10;">
                        <i class="fa-solid fa-camera" style="font-size: 16px;"></i>
                    </label>
                </div>
            </div>

            <div class="user-meta">
                <div class="name-row" style="display: flex; align-items: center; gap: 10px; margin-bottom: 12px;">
                    <h2 id="p-name" style="font-size: 26px; color: #1c1e21; font-weight: 700; margin: 0;">{{ $user->full_name }}</h2>
                    <div class="header-btns">
                        <button class="btn-edit-profile" onclick="window.switchProfileTab('info')" style="background: #e4e6eb; border: none; padding: 8px 16px; border-radius: 6px; font-weight: 600; cursor: pointer; color: #050505;">Hồ sơ của tôi</button>
                    </div>
                </div>
                
                <div class="stats-row" style="display: flex; gap: 30px; margin-bottom: 15px; font-size: 15px; color: #4b4b4b;">
                    <span><i class="fa-solid fa-heart" style="color: #e41e3f;"></i> <b>{{ $totalLikes }}</b> lượt thích</span>
                    <span><i class="fa-solid fa-user-group" style="color: #1877f2;"></i> <b>{{ $followingCount }}</b> đang theo dõi</span>
                </div>

                <div class="bio-section">
                    <div class="description" id="p-bio" style="color: #4b4b4b; font-size: 14px; white-space: pre-wrap;">{{ $user->bio ?? 'Chưa có tiểu sử.' }}</div>
                </div>
            </div>
        </div>

        <div class="profile-tabs" id="profile-tab-menu">
            <div class="tab active" data-tab="info" onclick="window.switchProfileTab('info')">
                <span class="tab-text">Thông tin</span> </div>
            <div class="tab" data-tab="data" onclick="window.switchProfileTab('data')">
                <span class="tab-text">Dữ liệu</span>
            </div>
            <div class="tab" data-tab="schedule" onclick="window.switchProfileTab('schedule')">
                <span class="tab-text">Lịch</span>
            </div>
            <div class="tab" data-tab="roles" onclick="window.switchProfileTab('roles')">
                <span class="tab-text">Vai trò</span>
            </div>
        </div>
    </div>

    <input type="file" id="upload-avatar-input" style="display: none;" onchange="window.handleImageUpload(this, 'avatar')">
    <input type="file" id="upload-cover-input" style="display: none;" onchange="window.handleImageUpload(this, 'cover')">

    <div id="profile-dynamic-content"></div>
</div>

<script>
    // Lưu mã sinh viên để dùng cho các request API
    window.studentCode = '{{ $user->student_code }}';

    window.executeDynamicScripts = function(container) {
        const scripts = container.querySelectorAll('script');
        scripts.forEach(oldScript => {
            const newScript = document.createElement('script');
            newScript.textContent = oldScript.textContent;
            document.body.appendChild(newScript);
            oldScript.remove();
        });
    };

    window.switchProfileTab = async function(tabName) {
        const tabContainer = document.getElementById('profile-tab-menu');
        const tabs = tabContainer.querySelectorAll('.tab');
        tabs.forEach(t => t.classList.remove('active'));
        const activeTab = tabContainer.querySelector(`.tab[data-tab="${tabName}"]`);
        if (activeTab) activeTab.classList.add('active');

        const contentBox = document.getElementById('profile-dynamic-content');
        contentBox.style.opacity = '0.5';

       try {
            // Gọi Route mới tạo, truyền kèm mã sinh viên và tên tab
            const noCacheUrl = `/profile/${window.studentCode}/tab/${tabName}?v=` + new Date().getTime();
            const response = await fetch(noCacheUrl);
            if (response.ok) {
                contentBox.innerHTML = await response.text();
                window.executeDynamicScripts(contentBox);
            } else {
                contentBox.innerHTML = '<div style="padding: 40px; text-align: center; color: #8e8e8e;">Không thể tải dữ liệu của tab này.</div>';
            }
        } catch (error) { 
            console.error("Lỗi khi chuyển tab:", error); 
        } finally { 
            contentBox.style.opacity = '1'; 
        }
    };

    window.handleImageUpload = async function(inputElement, type) {
        const file = inputElement.files[0];
        if (!file) return;
        const formData = new FormData();
        formData.append('image', file);
        formData.append('type', type);
        try {
            const response = await fetch(`/profile/${window.studentCode}/upload-image`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: formData
            });
            const result = await response.json();
            if (response.ok) {
                document.getElementById(type === 'avatar' ? 'p-avatar' : 'p-cover').src = result.url;
                alert("Đã cập nhật ảnh!");
            }
        } catch (error) { 
            alert("Lỗi kết nối máy chủ!"); 
        }
    };

    // Khởi chạy tab "Thông tin" mặc định khi vào trang Profile
    window.switchProfileTab('info');
</script>