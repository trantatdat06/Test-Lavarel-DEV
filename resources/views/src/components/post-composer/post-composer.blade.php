<style>
    /* KHUNG BÊN NGOÀI BẢNG TIN */
    .composer-wrapper { background: #fff; border-radius: 12px; box-shadow: 0 1px 2px rgba(0,0,0,0.05); border: 1px solid #e4e6eb; padding: 12px 16px 8px 16px; margin-bottom: 20px; }
    .post-composer-trigger { display: flex; align-items: center; cursor: pointer; margin-bottom: 12px; }
    .post-composer-trigger img { width: 40px; height: 40px; border-radius: 50%; object-fit: cover; }
    .post-composer-trigger input { flex: 1; border: none; background: #f0f2f5; padding: 10px 15px; border-radius: 20px; font-size: 15px; cursor: pointer; color: #65676b; outline: none; margin-left: 10px; transition: 0.2s; }
    .post-composer-trigger input:hover { background: #e4e6eb; }

    .composer-action-bar { display: flex; align-items: center; justify-content: space-between; border-top: 1px solid #e4e6eb; padding-top: 8px; }
    .composer-action-btn { flex: 1; display: flex; align-items: center; justify-content: center; gap: 8px; padding: 8px; border-radius: 8px; color: #65676b; font-weight: 600; font-size: 14px; text-decoration: none; transition: 0.2s; cursor: pointer; border: none; background: transparent; }
    .composer-action-btn:hover { background: #f0f2f5; color: #1c1e21; }

    /* MODAL ĐĂNG BÀI */
    .post-modal-overlay { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(255, 255, 255, 0.85); display: flex; justify-content: center; align-items: flex-start; padding-top: 40px; z-index: 9999; backdrop-filter: blur(4px); overflow-y: auto; }
    .post-modal-container { background: #fff; width: 100%; max-width: 600px; border-radius: 12px; box-shadow: 0 12px 28px rgba(0, 0, 0, 0.15); display: flex; flex-direction: column; margin-bottom: 50px; }
    
    .post-modal-header { padding: 18px 20px; border-bottom: 1px solid #e4e6eb; display: flex; align-items: center; position: relative; }
    .post-modal-header h3 { font-size: 20px; font-weight: 700; margin: 0; text-align: center; width: 100%; color: #1c1e21; }
    .btn-close-modal { position: absolute; right: 15px; background: #e4e6eb; border: none; width: 36px; height: 36px; border-radius: 50%; cursor: pointer; color: #65676b; display: flex; align-items: center; justify-content: center; font-size: 18px; transition: 0.2s; }
    .btn-close-modal:hover { background: #d8dadf; }
    
    .post-modal-body { padding: 20px 24px; max-height: 80vh; overflow-y: auto; }
    
    .user-info-row { display: flex; gap: 12px; margin-bottom: 15px; align-items: center; }
    .author-avatar-modal { width: 45px; height: 45px; border-radius: 50%; object-fit: cover; }
    .modal-author-name { font-weight: 700; font-size: 15px; color: #1c1e21; }
    .visibility-select { background: #e4e6eb; padding: 4px 8px; border-radius: 6px; font-size: 13px; font-weight: 600; border: none; outline: none; margin-top: 4px; cursor: pointer; }

    .post-input-textarea { width: 100%; min-height: 120px; border: none; outline: none; font-size: 16px; resize: none; font-family: inherit; color: #1c1e21; margin-bottom: 20px; line-height: 1.5; }
    
    /* TOOLBAR & ADDON PANELS */
    .add-to-post-toolbar { display: flex; align-items: center; justify-content: space-between; padding: 10px 15px; border: 1px solid #e4e6eb; border-radius: 8px; margin-bottom: 15px; box-shadow: 0 1px 2px rgba(0,0,0,0.05); }
    .add-to-post-toolbar span { font-weight: 600; font-size: 14px; color: #1c1e21; }
    .addon-buttons { display: flex; gap: 8px; }
    .addon-btn { background: #f0f2f5; border: none; padding: 8px 12px; border-radius: 6px; font-weight: 600; font-size: 14px; color: #65676b; cursor: pointer; display: flex; align-items: center; gap: 6px; transition: 0.2s; }
    .addon-btn:hover { background: #e4e6eb; }
    .addon-btn.active { background: #e0f2fe; color: #2563eb; }

    .addon-panel { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 15px; margin-bottom: 15px; position: relative; }
    .addon-panel-title { font-size: 14px; font-weight: 700; margin-bottom: 12px; display: flex; align-items: center; gap: 6px; }
    .btn-remove-addon { position: absolute; top: 12px; right: 12px; background: transparent; border: none; color: #94a3b8; cursor: pointer; font-size: 16px; }
    .btn-remove-addon:hover { color: #ef4444; }

    .form-group { margin-bottom: 12px; }
    .form-group label { display: block; font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 6px; }
    .form-control { width: 100%; border: 1px solid #d1d5db; padding: 10px 12px; border-radius: 6px; font-size: 14px; outline: none; box-sizing: border-box; background: #fff; }
    .form-control:focus { border-color: #4f46e5; box-shadow: 0 0 0 2px rgba(79, 70, 229, 0.1); }

    .post-modal-footer { padding: 15px 24px; border-top: 1px solid #e4e6eb; }
    .btn-post-submit { width: 100%; padding: 12px; border: none; border-radius: 6px; background: #1877f2; color: #fff; font-weight: 700; cursor: pointer; font-size: 15px; transition: 0.2s; }
    .btn-post-submit:hover:not(:disabled) { background: #166fe5; }
    .btn-post-submit:disabled { background: #e4e6eb; color: #bcc0c4; cursor: not-allowed; }
    
    .animate-slide-down { animation: slideDownModal 0.2s ease-out forwards; }
    @keyframes slideDownModal { from { transform: translateY(-30px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }

    /* CSS cho Tag chọn tự do */
    .tag-selector { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 8px; margin-bottom: 15px; }
    .tag-checkbox { display: none; /* Ẩn ô vuông checkbox mặc định đi */}
    .tag-label { display: inline-block; padding: 6px 14px; background-color: #f0f2f5; color: #65676b; border-radius: 20px; font-size: 13px; cursor: pointer; transition: all 0.2s ease; border: 1px solid transparent; user-select: none; /* Ngăn bôi đen text khi click nhầm */}
    .tag-label:hover {background-color: #e4e6eb; }
    /* Đổi màu xanh kh i người dùng bấm chọn (checked) */
    .tag-checkbox:checked + .tag-label {background-color: #e7f3ff; color: #1877f2; border-color: #1877f2; font-weight: 500; }
</style>

<div class="composer-wrapper">
    <div class="post-composer-trigger" onclick="openCreatePostModal(this)">
        <img class="shared-avatar-composer" src="" alt="Avatar">
        <input type="text" placeholder="Bạn muốn chia sẻ thông báo, sự kiện gì?" readonly>
    </div>

    <div class="composer-action-bar">
        <button type="button" class="composer-action-btn" onclick="openCreatePostModal(this)">
            <i class="fa-solid fa-image" style="color: #45bd62; font-size: 20px;"></i> Đăng Bài / Ảnh
        </button>
        <a href="{{ route('events.create') ?? '/events/create' }}" class="composer-action-btn">
            <i class="fa-solid fa-calendar-plus" style="color: #dc2626; font-size: 20px;"></i> Tạo Sự kiện
        </a>
        <a href="{{ route('forms.create') ?? '/forms/create' }}" class="composer-action-btn">
            <i class="fa-solid fa-file-signature" style="color: #166534; font-size: 20px;"></i> Tạo Form
        </a>
    </div>
</div>

<div class="post-modal-overlay" style="display: none;">
    <form action="/posts" method="POST" enctype="multipart/form-data" class="post-modal-container animate-slide-down">
        @csrf 
        <input type="hidden" name="post_type" value="post">
        <input type="hidden" name="has_event" id="input_has_event" value="0">
        <input type="hidden" name="has_form" id="input_has_form" value="0">
        
        <div class="post-modal-header">
            <h3>Tạo bài viết</h3>
            <button type="button" class="btn-close-modal" onclick="closeCreatePostModal(this)"><i class="fa-solid fa-xmark"></i></button>
        </div>

        <div class="post-modal-body">
            <div class="user-info-row">
                <img class="modal-user-avatar" src="" alt="Avatar" class="author-avatar-modal">
                <div class="author-meta">
                    <div class="modal-author-name">Người dùng</div>
                    <select name="visibility" class="visibility-select">
                        <option value="public"><i class="fa-solid fa-earth-americas"></i> Công khai</option>
                        <option value="private"><i class="fa-solid fa-lock"></i> Riêng tư</option>
                    </select>
                </div>
            </div>
            
            <textarea name="content" class="post-input-textarea" required placeholder="Nội dung bài viết của bạn..." oninput="this.closest('form').querySelector('.btn-post-submit').disabled = this.value.trim() === '';"></textarea>
            
            <div id="panel_event" class="addon-panel" style="display: none; border-color: #fca5a5; background: #fef2f2;">
                <button type="button" class="btn-remove-addon" onclick="toggleAddon('event', false)"><i class="fa-solid fa-xmark"></i></button>
                <div class="addon-panel-title" style="color: #dc2626;"><i class="fa-solid fa-calendar-check"></i> Đính kèm Sự kiện</div>
                <div class="form-group">
                    <label>Chọn Sự kiện từ danh sách <span style="color:red">*</span></label>
                    <select name="attached_event_id" class="form-control">
                        <option value="">-- Chọn sự kiện bạn đã tạo --</option>
                        @if(isset($availableEvents) && count($availableEvents) > 0)
                            @foreach($availableEvents as $ev)
                                <option value="{{ $ev->id }}">{{ $ev->title }}</option>
                            @endforeach
                        @else
                            <option value="" disabled>Bạn chưa có Sự kiện nào rảnh rỗi. Hãy ra ngoài tạo nhé!</option>
                        @endif
                    </select>
                </div>
            </div>

            <div id="panel_form" class="addon-panel" style="display: none; border-color: #86efac; background: #f0fdf4;">
                <button type="button" class="btn-remove-addon" onclick="toggleAddon('form', false)"><i class="fa-solid fa-xmark"></i></button>
                <div class="addon-panel-title" style="color: #166534;"><i class="fa-solid fa-clipboard-check"></i> Đính kèm Biểu mẫu</div>
                <div class="form-group">
                    <label>Chọn Form từ danh sách <span style="color:red">*</span></label>
                    <select name="attached_form_id" class="form-control">
                        <option value="">-- Chọn Biểu mẫu bạn đã tạo --</option>
                        @if(isset($availableForms) && count($availableForms) > 0)
                            @foreach($availableForms as $fm)
                                <option value="{{ $fm->id }}">{{ $fm->title }}</option>
                            @endforeach
                        @else
                            <option value="" disabled>Bạn chưa tạo Form nào. Hãy ra ngoài tạo nhé!</option>
                        @endif
                    </select>
                </div>
            </div>

            <div class="addon-section">
                <div class="addon-title"><i class="fa-solid fa-image"></i> Ảnh đính kèm</div>
                <input type="file" name="media" accept="image/*" class="form-control" style="padding: 6px;">
            </div>

            <div class="addon-section" style="margin-bottom: 0;">
                <div class="addon-title"><i class="fa-solid fa-tags"></i> Chủ đề bài viết (Tags)</div>
                <p style="font-size: 12px; color: #65676b; margin-top: 4px; margin-bottom: 8px;">Nhấn vào các thẻ bên dưới để chọn (hoặc bỏ chọn) chủ đề</p>
                
                <div class="tag-selector">
                    @if(isset($availableTags) && count($availableTags) > 0)
                        @foreach($availableTags as $tag)
                            <input type="checkbox" name="tags[]" id="tag_{{ $tag->id }}" value="{{ $tag->name }}" class="tag-checkbox">
                            <label for="tag_{{ $tag->id }}" class="tag-label">#{{ $tag->name }}</label>
                        @endforeach
                    @else
                        <p style="font-size: 13px; color: #65676b; font-style: italic;">Chưa có chủ đề nào trong hệ thống</p>
                    @endif
                </div>

                <div class="addon-title" style="margin-top: 15px;"><i class="fa-solid fa-link"></i> Link sự kiện/form đính kèm</div>
                <input type="url" name="external_link" class="form-control" placeholder="Dán link sự kiện/form vào đây (nếu có)..." style="margin-bottom: 10px;">
            </div>
            
        </div>
        <div class="post-modal-footer">
            <button type="submit" class="btn-post-submit" disabled>Đăng bài</button>
        </div>
    </form>
</div>

<script>
    (function initComposer() {
        const user = window.currentProfileUser || {};
        const avatarUrl = user.avatar || `https://ui-avatars.com/api/?name=${user.name || 'User'}&background=random`;
        document.querySelectorAll('.shared-avatar-composer, .modal-user-avatar').forEach(img => img.src = avatarUrl);
        document.querySelectorAll('.modal-author-name').forEach(name => name.innerText = user.name || "Admin Page");
    })();

    window.openCreatePostModal = function(triggerElement) {
        const wrapper = triggerElement.closest('.composer-wrapper');
        const modal = wrapper.nextElementSibling;
        if(modal && modal.classList.contains('post-modal-overlay')) {
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
            setTimeout(() => modal.querySelector('.post-input-textarea').focus(), 100);
        }
    };

    window.closeCreatePostModal = function(btnElement) { 
        const modal = btnElement.closest('.post-modal-overlay');
        if(modal) { modal.style.display = 'none'; document.body.style.overflow = 'auto'; }
    };

    window.addEventListener('click', function(e) { 
        if(e.target.classList.contains('post-modal-overlay')) {
            e.target.style.display = 'none'; document.body.style.overflow = 'auto';
        }
    });

    // Bật tắt Panel & Gắn Require cho Select
    window.toggleAddon = function(type, isShow) {
        const panel = document.getElementById('panel_' + type);
        const btn = document.getElementById('btn_toggle_' + type);
        
        if (isShow) {
            panel.style.display = 'block';
            btn.classList.add('active');
        } else {
            panel.style.display = 'none';
            btn.classList.remove('active');
        }

        if (type === 'event') {
            document.getElementById('input_has_event').value = isShow ? '1' : '0';
            const selectEvent = panel.querySelector('select[name="attached_event_id"]');
            isShow ? selectEvent.setAttribute('required', 'required') : selectEvent.removeAttribute('required');
            if(!isShow) selectEvent.value = ""; // Xóa giá trị khi tắt
        }
        
        if (type === 'form') {
            document.getElementById('input_has_form').value = isShow ? '1' : '0';
            const selectForm = panel.querySelector('select[name="attached_form_id"]');
            isShow ? selectForm.setAttribute('required', 'required') : selectForm.removeAttribute('required');
            if(!isShow) selectForm.value = "";
        }
    };
</script>