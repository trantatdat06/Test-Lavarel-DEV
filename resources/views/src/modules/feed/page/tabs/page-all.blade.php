<div class="profile-two-column" id="profile-all-layout">
    <div class="profile-left-col">
        <div class="info-card">
            <div class="info-card-header">
                <h3>Thông tin Trang</h3>
                <span class="btn-toggle-info" id="toggle-info-btn" onclick="toggleInfoEdit()">Mở rộng</span>
            </div>
            <div id="info-normal-view">
                <div class="info-item"><span class="label">Loại trang</span><span class="value">Giáo dục & Học tập</span></div>
                <div class="info-item"><span class="label">ID Trang</span><span class="value">PAGE-2026-6BAV</span></div>
                <div class="info-item"><span class="label">Ngày thành lập</span><span class="value">22/02/2026</span></div>
                
                <div class="info-item extra-info"><span class="label">Quản lý bởi</span><span class="value">Nhóm 6 BAV</span></div>
                <div class="info-item extra-info"><span class="label">Trạng thái</span><span class="value"><i class="fa-solid fa-circle-check"></i> Đang hoạt động</span></div>
                <div class="info-item extra-info"><span class="label">Lĩnh vực</span><span class="value">Trí tuệ nhân tạo (AI)</span></div>
            </div>
            
            <div id="info-expanded-view" style="display: none;">
                <div class="info-expanded-layout">
                    <div class="info-expanded-sidebar">
                        <button class="info-tab-btn active">Tổng quan</button>
                        <button class="info-tab-btn">Liên hệ</button>
                    </div>
                    <div class="info-expanded-content">
                        <img src="https://static-maps.yandex.ru/1.x/?lang=en-US&ll=105.8282,21.0064&z=15&l=map&size=600,200&pt=105.8282,21.0064,pm2rdm" alt="Map" class="map-placeholder">
                        <div class="info-detail-row"><i class="fa-solid fa-location-dot"></i> 12, Chùa Bộc, Đống Đa, Hà Nội</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="profile-right-col">
        <div class="profile-feed-header">
            <div id="shared-post-composer-placeholder"></div>
            <div id="shared-feed-filters-placeholder"></div>
        </div>
        <div id="profile-posts-list"></div>
    </div>
</div>

<style>
    /* Logic ẩn các item từ thứ 4 trở đi trên iPad/Mobile */
    @media (max-width: 1250px) {
        #profile-all-layout:not(.expanded-info) .extra-info {
            display: none !important;
        }
    }
</style>

<script>
    window.updateToggleButtonText = function() {
        const toggleBtn = document.getElementById('toggle-info-btn');
        const layout = document.getElementById('profile-all-layout');
        const isSmallScreen = window.innerWidth < 1250;

        if (layout.classList.contains('expanded-info')) {
            toggleBtn.innerText = 'Thu nhỏ';
        } else {
            toggleBtn.innerText = isSmallScreen ? 'Xem thêm' : 'Mở rộng';
        }
    };

    window.toggleInfoEdit = function() {
        const layout = document.getElementById('profile-all-layout');
        const normalView = document.getElementById('info-normal-view');
        const expandedView = document.getElementById('info-expanded-view');

        if (layout.classList.contains('expanded-info')) {
            layout.classList.remove('expanded-info');
            normalView.style.display = 'block'; 
            expandedView.style.display = 'none';
        } else {
            layout.classList.add('expanded-info');
            normalView.style.display = 'none'; 
            expandedView.style.display = 'block';
        }
        updateToggleButtonText();
    };

    window.updateToggleButtonText();
    window.addEventListener('resize', window.updateToggleButtonText);

    (async function loadAllComponents() {
        try {
            const [resComp, resFilt, resItem] = await Promise.all([
                fetch('{{ url('src/components/post-composer/post-composer.blade.php') }}'),
                fetch('{{ url('src/components/feed-filters/feed-filters.blade.php') }}'),
                fetch('{{ url('src/components/post-item/post-item.blade.php') }}')
            ]);
            
            if(resComp.ok) {
                const html = await resComp.text();
                const placeholder = document.getElementById('shared-post-composer-placeholder');
                placeholder.innerHTML = html;
                if(typeof window.executeDynamicScripts === 'function') window.executeDynamicScripts(placeholder);
            }
            if(resFilt.ok) document.getElementById('shared-feed-filters-placeholder').innerHTML = await resFilt.text();
            
            if(resItem.ok) {
                const template = await resItem.text();
                const container = document.getElementById('profile-posts-list');
                const samplePosts = [{
                    name: "Trang Dự Án AI",
                    avatar: "https://ui-avatars.com/api/?name=AI&background=4a66f0&color=fff&size=150",
                    time: "1 giờ trước",
                    content: "Chào mừng mọi người đến với Trang thông tin chính thức của Dự án Hệ sinh thái Học tập AI Nhóm 6!",
                    image: "https://images.unsplash.com/photo-1677442136019-21780ecad995?q=80&w=1000"
                }];

                samplePosts.forEach(data => {
                    const tempDiv = document.createElement('div');
                    tempDiv.innerHTML = template;
                    const nameEl = tempDiv.querySelector('.post-data-name');
                    const avatarEl = tempDiv.querySelector('.post-data-avatar');
                    const timeEl = tempDiv.querySelector('.post-data-time');
                    const contentEl = tempDiv.querySelector('.post-data-content');
                    const mediaCont = tempDiv.querySelector('.post-data-media-container');
                    const imgEl = tempDiv.querySelector('.post-data-image');

                    if(nameEl) nameEl.innerText = data.name;
                    if(avatarEl) avatarEl.src = data.avatar;
                    if(timeEl) timeEl.innerText = data.time;
                    if(contentEl) contentEl.innerText = data.content;
                    if(data.image && mediaCont && imgEl) {
                        mediaCont.style.display = 'block';
                        imgEl.src = data.image;
                    }
                    while (tempDiv.firstChild) { container.appendChild(tempDiv.firstChild); }
                });
            }
        } catch (err) { console.error("Lỗi tải component Page:", err); }
    })();
</script>