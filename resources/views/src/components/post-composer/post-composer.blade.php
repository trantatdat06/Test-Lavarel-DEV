<style>
    .post-composer { display: flex; align-items: center; background: #fff; padding: 12px 16px; border-radius: 12px; border: 1px solid #e4e6eb; cursor: pointer; transition: 0.2s; }
    .post-composer:hover { background: #f2f2f2; }
    .post-composer img { width: 40px; height: 40px; border-radius: 50%; object-fit: cover; }
    .post-composer input { flex: 1; border: none; background: #f0f2f5; padding: 10px 15px; border-radius: 20px; font-size: 15px; cursor: pointer; color: #65676b; outline: none; }

    .post-modal-overlay { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(255, 255, 255, 0.85); display: flex; justify-content: center; align-items: flex-start; padding-top: 60px; z-index: 9999; backdrop-filter: blur(5px); }
    .post-modal-container { background: #fff; width: 100%; max-width: 500px; border-radius: 8px; box-shadow: 0 12px 28px rgba(0, 0, 0, 0.15); overflow: hidden; display: flex; flex-direction: column; }
    .post-modal-header { padding: 15px; border-bottom: 1px solid #e4e6eb; display: flex; align-items: center; }
    .post-modal-header h3 { font-size: 20px; font-weight: 700; margin: 0; text-align: center; flex: 1; color: #1c1e21; }
    .btn-close-modal { background: #e4e6eb; border: none; width: 36px; height: 36px; border-radius: 50%; cursor: pointer; color: #65676b; display: flex; align-items: center; justify-content: center; font-size: 18px; }
    .post-modal-body { padding: 15px 20px; }
    .user-info-row { display: flex; gap: 12px; margin-bottom: 15px; }
    .author-avatar-modal { width: 40px; height: 40px; border-radius: 50%; object-fit: cover; }
    .modal-author-name { font-weight: 700; font-size: 15px; color: #1c1e21; }
    .post-input-textarea { width: 100%; min-height: 160px; border: none; outline: none; font-size: 22px; resize: none; font-family: inherit; color: #1c1e21; }
    .add-to-post-box { border: 1px solid #e4e6eb; border-radius: 8px; padding: 10px 15px; display: flex; justify-content: space-between; align-items: center; margin-top: 15px; }
    .add-text { font-weight: 600; font-size: 15px; color: #1c1e21; }
    .add-icons { display: flex; gap: 5px; }
    .add-icon-item { width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 20px; cursor: pointer; }
    .add-icon-item:hover { background: #f2f2f2; }
    .post-modal-footer { padding: 15px; }
    .btn-post-submit { width: 100%; padding: 10px; border: none; border-radius: 6px; background: #1877f2; color: #fff; font-weight: 700; cursor: pointer; font-size: 15px; }
    .btn-post-submit:disabled { background: #e4e6eb; color: #bcc0c4; cursor: not-allowed; }
    .animate-slide-down { animation: slideDownModal 0.2s ease-out forwards; }
    @keyframes slideDownModal { from { transform: translateY(-30px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
</style>

<div class="post-composer" onclick="openCreatePostModal()">
    <img id="shared-avatar-composer" src="" alt="Avatar">
    <input type="text" placeholder="Bạn muốn chia sẻ thành tựu hoặc bài viết gì?" readonly>
</div>

<div id="post-creator-modal" class="post-modal-overlay" style="display: none;">
    <div class="post-modal-container animate-slide-down">
        <div class="post-modal-header">
            <h3>Tạo bài viết</h3>
            <button class="btn-close-modal" onclick="closeCreatePostModal(event)"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="post-modal-body">
            <div class="user-info-row">
                <img id="modal-user-avatar" src="" alt="Avatar" class="author-avatar-modal">
                <div class="author-meta">
                    <div class="modal-author-name" id="modal-user-name">Người dùng</div>
                    <div class="privacy-selector" style="background: #e4e6eb; padding: 4px 8px; border-radius: 6px; font-size: 13px; font-weight: 600;">
                        <i class="fa-solid fa-earth-americas"></i> <span>Công khai</span> <i class="fa-solid fa-caret-down"></i>
                    </div>
                </div>
            </div>
            <textarea class="post-input-textarea" id="post-main-input"></textarea>
            <div class="add-to-post-box">
                <span class="add-text">Thêm vào bài viết của bạn</span>
                <div class="add-icons">
                    <div class="add-icon-item" title="Ảnh"><i class="fa-solid fa-images" style="color: #45bd62;"></i></div>
                    <div class="add-icon-item" title="Tag"><i class="fa-solid fa-user-tag" style="color: #1877f2;"></i></div>
                    <div class="add-icon-item" title="Checkin"><i class="fa-solid fa-location-dot" style="color: #f5533d;"></i></div>
                </div>
            </div>
        </div>
        <div class="post-modal-footer"><button class="btn-post-submit" id="btn-post-publish" disabled>Đăng</button></div>
    </div>
</div>

<script>
    (function initComposer() {
        const user = window.currentProfileUser || {};
        const avatarUrl = user.avatar || `https://ui-avatars.com/api/?name=${user.name || 'User'}&background=random`;
        document.getElementById('shared-avatar-composer').src = avatarUrl;
        document.getElementById('modal-user-avatar').src = avatarUrl;
        document.getElementById('modal-user-name').innerText = user.name || "Người dùng";
        if(user.name) {
            const firstName = user.name.split(' ').pop();
            document.getElementById('post-main-input').placeholder = `${firstName} ơi, bạn đang nghĩ gì thế?`;
        }
    })();

    window.openCreatePostModal = function() {
        const modal = document.getElementById('post-creator-modal');
        if(modal) {
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
            setTimeout(() => document.getElementById('post-main-input').focus(), 100);
        }
    };

    window.closeCreatePostModal = function(e) { if(e) e.stopPropagation(); document.getElementById('post-creator-modal').style.display = 'none'; document.body.style.overflow = 'auto'; };

    window.addEventListener('click', function(e) { if(e.target === document.getElementById('post-creator-modal')) closeCreatePostModal(); });

    document.getElementById('post-main-input').addEventListener('input', function() {
        document.getElementById('btn-post-publish').disabled = this.value.trim() === '';
    });
</script>