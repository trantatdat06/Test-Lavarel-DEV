<div class="profile-two-column" id="profile-all-layout">
    <div class="profile-left-col">
        <div class="info-card">
            <div class="info-card-header">
                <h3>Thông tin</h3>
                <span class="btn-toggle-info" id="toggle-info-btn" onclick="toggleInfoEdit()">Mở rộng</span>
            </div>
            <div id="info-normal-view">
                <div class="info-item"><span class="label">Email trường</span><span class="value" id="tab-email">---</span></div>
                <div class="info-item"><span class="label">Mã sinh viên</span><span class="value" id="tab-msv">---</span></div>
                <div class="info-item"><span class="label">Lớp sinh hoạt</span><span class="value" id="tab-class">---</span></div>
                <div class="info-item"><span class="label">Trạng thái xác thực</span><span class="value readonly"><i class="fa-solid fa-shield-check"></i> Đã hoàn tất</span></div>
                <div style="height: 1px; background: #f0f2f5; margin: 15px 0;"></div>
                <div class="info-item"><span class="label">Dự án đang thực hiện</span><span class="value">Hệ sinh thái Mạng xã hội Học tập tích hợp AI</span></div>
                <div class="info-item"><span class="label">Giảng viên hướng dẫn</span><span class="value">Cô Giang Thị Thu Huyền</span></div>
            </div>
            <div id="info-expanded-view" style="display: none;">
                <div class="info-expanded-layout">
                    <div class="info-expanded-sidebar">
                        <button class="info-tab-btn active">Thông tin cá nhân</button>
                        <button class="info-tab-btn">Thông tin liên hệ</button>
                        <button class="info-tab-btn">Mối quan tâm</button>
                    </div>
                    <div class="info-expanded-content">
                        <img src="https://static-maps.yandex.ru/1.x/?lang=en-US&ll=105.8282,21.0064&z=15&l=map&size=600,200&pt=105.8282,21.0064,pm2rdm" alt="Map BAV" class="map-placeholder">
                        <div class="info-detail-row"><i class="fa-solid fa-circle-info"></i> Sinh viên Học viện Ngân hàng - Khoa CNTT</div>
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

<script>
    window.toggleInfoEdit = function() {
        const layout = document.getElementById('profile-all-layout');
        const normalView = document.getElementById('info-normal-view');
        const expandedView = document.getElementById('info-expanded-view');
        const toggleBtn = document.getElementById('toggle-info-btn');

        if (layout.classList.contains('expanded-info')) {
            layout.classList.remove('expanded-info');
            normalView.style.display = 'block'; expandedView.style.display = 'none';
            toggleBtn.innerText = window.innerWidth <= 890 ? 'Xem thêm' : 'Mở rộng';
        } else {
            layout.classList.add('expanded-info');
            normalView.style.display = 'none'; expandedView.style.display = 'block';
            toggleBtn.innerText = 'Thu nhỏ';
        }
    };

    (async function loadAllComponents() {
        try {
            // Cập nhật đường dẫn fetch sang chuẩn .blade.php
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
                    name: window.currentProfileUser.name || "Trần Tất Đạt",
                    avatar: window.currentProfileUser.avatar || "",
                    time: "2 giờ trước",
                    content: "Hoàn thành xong phase 1 của dự án Mạng xã hội Học tập AI! 🚀",
                    image: "https://images.unsplash.com/photo-1498050108023-c5249f4df085?q=80&w=1000"
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
                    if(avatarEl) avatarEl.src = data.avatar || `https://ui-avatars.com/api/?name=${data.name}&background=random`;
                    if(timeEl) timeEl.innerText = data.time;
                    if(contentEl) contentEl.innerText = data.content;
                    if(data.image && mediaCont && imgEl) {
                        mediaCont.style.display = 'block';
                        imgEl.src = data.image;
                    }
                    
                    while (tempDiv.firstChild) {
                        container.appendChild(tempDiv.firstChild);
                    }
                });
            }
        } catch (err) { console.error("Lỗi tải component:", err); }
    })();

    (function fillData() {
        const user = window.currentProfileUser || {};
        if(document.getElementById('tab-email')) document.getElementById('tab-email').innerText = user.email || "---";
        if(document.getElementById('tab-msv')) document.getElementById('tab-msv').innerText = user.msv || "---";
        if(document.getElementById('tab-class')) document.getElementById('tab-class').innerText = user.className || "K27HTTTB";
    })();
</script>