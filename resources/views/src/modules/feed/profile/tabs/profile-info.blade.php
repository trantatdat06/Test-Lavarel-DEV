<style>
    .info-wireframe-layout {
        display: flex; background: #fff; border-radius: 16px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05); margin-top: 20px;
        overflow: hidden; animation: fadeInUp 0.3s ease; min-height: 500px;
    }
    .info-sidebar { width: 240px; background: #f8f9fa; border-right: 1px solid #e4e6eb; padding: 20px 0; flex-shrink: 0; }
    .info-sidebar-title { font-size: 18px; font-weight: 700; color: #1c1e21; padding: 0 20px 15px; border-bottom: 1px solid #e4e6eb; margin-bottom: 10px; }
    .info-menu-item { padding: 12px 20px; font-size: 15px; font-weight: 600; color: #65676b; cursor: pointer; transition: 0.2s; display: flex; align-items: center; gap: 10px; }
    .info-menu-item:hover { background: #eef2ff; color: #1877f2; }
    .info-menu-item.active { background: #e7f3ff; color: #1877f2; border-right: 3px solid #1877f2; }

    .info-content-area { flex: 1; padding: 20px 30px; }
    .info-header-action { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; padding-bottom: 15px; border-bottom: 1px solid #f0f2f5; }
    .info-section-title { font-size: 18px; font-weight: 700; color: #1c1e21; }
    .btn-toggle-edit { background: #e7f3ff; color: #1877f2; border: none; padding: 8px 16px; border-radius: 6px; font-weight: 600; cursor: pointer; transition: 0.2s; }
    
    .data-row { display: flex; align-items: center; padding: 15px 0; border-bottom: 1px dashed #f0f2f5; }
    .data-row:last-child { border-bottom: none; }
    .col-label { width: 30%; color: #65676b; font-size: 14px; font-weight: 600; }
    .col-value { width: 50%; color: #1c1e21; font-size: 15px; font-weight: 500; padding-right: 20px; }
    .col-status { width: 20%; display: flex; justify-content: flex-end; }

    .edit-input, .edit-select { width: 100%; padding: 8px 12px; border: 1px solid #ccd0d5; border-radius: 6px; font-family: inherit; font-size: 14px; outline: none; transition: 0.2s; }
    .edit-input:focus, .edit-select:focus { border-color: #1877f2; }
    .input-readonly { background: #f0f2f5; color: #8e8e8e; cursor: not-allowed; border-color: transparent; }

    .privacy-badge { display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; background: #f0f2f5; border-radius: 20px; font-size: 12px; font-weight: 600; color: #4b4b4b; cursor: pointer; border: none; outline: none; }
    select.privacy-badge { cursor: pointer; -webkit-appearance: none; padding-right: 25px; background: #f0f2f5 url("data:image/svg+xml;utf8,<svg fill='black' height='24' viewBox='0 0 24 24' width='24' xmlns='http://www.w3.org/2000/svg'><path d='M7 10l5 5 5-5z'/><path d='M0 0h24v24H0z' fill='none'/></svg>") no-repeat right 5px center / 16px; }

    .action-buttons { display: none; gap: 10px; margin-top: 25px; justify-content: flex-end; }
    .btn-cancel { background: #f0f2f5; color: #1c1e21; border: none; padding: 10px 20px; border-radius: 6px; font-weight: 600; cursor: pointer; }
    .btn-save { background: #1877f2; color: #fff; border: none; padding: 10px 20px; border-radius: 6px; font-weight: 600; cursor: pointer; }

    /* Nút Thêm Trường Dữ Liệu */
    .add-field-wrapper { display: none; margin-top: 15px; text-align: center; border: 1px dashed #ccd0d5; padding: 20px; border-radius: 8px; background: #fafafa; }
    .add-field-select { padding: 8px 12px; border-radius: 6px; border: 1px solid #1877f2; outline: none; margin-right: 10px; font-family: inherit; font-weight: 500; }

    @media (max-width: 768px) {
        .info-wireframe-layout { flex-direction: column; }
        .info-sidebar { width: 100%; display: flex; overflow-x: auto; padding: 10px 0; border-right: none; }
        .info-menu-item { white-space: nowrap; border-bottom: 3px solid transparent; }
        .info-menu-item.active { border-bottom: 3px solid #1877f2; border-right: none; }
        .data-row { flex-direction: column; align-items: flex-start; gap: 10px; }
        .col-label, .col-value, .col-status { width: 100%; justify-content: flex-start; }
    }
</style>

<div class="info-wireframe-layout">
    <div class="info-sidebar">
        <div class="info-sidebar-title">Hồ sơ cá nhân</div>
        <div class="info-menu-item active"><i class="fa-solid fa-address-card"></i> Thông tin cơ bản</div>
        <div class="info-menu-item"><i class="fa-solid fa-briefcase"></i> Công việc & Học vấn</div>
        <div class="info-menu-item"><i class="fa-solid fa-link"></i> Liên kết MXH</div>
    </div>

    <div class="info-content-area" id="info-form-container">
        <div class="info-header-action">
            <div class="info-section-title">Dữ liệu định danh (Hỗ trợ tự động điền Form)</div>
            <button class="btn-toggle-edit" onclick="toggleEditWireframe()"><i class="fa-solid fa-pen"></i> Chỉnh sửa hồ sơ</button>
        </div>

        <div class="data-table-wrapper" id="dynamic-fields-container">
            <div class="data-row" style="padding-top: 0; padding-bottom: 10px; border-bottom: 2px solid #f0f2f5;">
                <div class="col-label" style="color: #1c1e21; font-size: 13px;">TRƯỜNG DỮ LIỆU</div>
                <div class="col-value" style="color: #1c1e21; font-size: 13px;">NỘI DUNG</div>
                <div class="col-status" style="color: #1c1e21; font-size: 13px; justify-content: center;">BẢO MẬT</div>
            </div>

            <div class="data-row" id="row-email">
                <div class="col-label">Email trường</div>
                <div class="col-value">
                    <span class="view-elm" id="w-email">---</span>
                    <input type="text" class="edit-input edit-elm input-readonly" id="w-email-input" disabled style="display: none;">
                </div>
                <div class="col-status"><button class="privacy-badge" disabled><i class="fa-solid fa-lock"></i> Mặc định</button></div>
            </div>

            <div class="data-row" id="row-msv">
                <div class="col-label">Mã sinh viên</div>
                <div class="col-value">
                    <span class="view-elm" id="w-msv">---</span>
                    <input type="text" class="edit-input edit-elm input-readonly" id="w-msv-input" disabled style="display: none;">
                </div>
                <div class="col-status"><button class="privacy-badge" disabled><i class="fa-solid fa-lock"></i> Mặc định</button></div>
            </div>

            <div class="data-row" id="row-name">
                <div class="col-label">Họ và tên hiển thị</div>
                <div class="col-value">
                    <span class="view-elm" id="w-name">---</span>
                    <input type="text" class="edit-input edit-elm" id="w-name-input" style="display: none;">
                </div>
                <div class="col-status">
                    <span class="privacy-badge view-elm" id="priv-view-name">---</span>
                    <select class="privacy-badge edit-elm" id="priv-edit-name" style="display: none;">
                        <option value="public">Công khai</option><option value="friends">Bạn bè</option><option value="private">Chỉ mình tôi</option>
                    </select>
                </div>
            </div>

            <div class="data-row" id="row-bio">
                <div class="col-label">Tiểu sử</div>
                <div class="col-value">
                    <span class="view-elm" id="w-bio">---</span>
                    <input type="text" class="edit-input edit-elm" id="w-bio-input" placeholder="Viết gì đó về bạn..." style="display: none;">
                </div>
                <div class="col-status">
                    <span class="privacy-badge view-elm" id="priv-view-bio">---</span>
                    <select class="privacy-badge edit-elm" id="priv-edit-bio" style="display: none;">
                        <option value="public">Công khai</option><option value="private">Chỉ mình tôi</option>
                    </select>
                </div>
            </div>

            <div class="data-row" id="row-job">
                <div class="col-label">Công việc</div>
                <div class="col-value">
                    <span class="view-elm" id="w-job">---</span>
                    <input type="text" class="edit-input edit-elm" id="w-job-input" placeholder="VD: Thực tập sinh BA, Lập trình viên..." style="display: none;">
                </div>
                <div class="col-status">
                    <span class="privacy-badge view-elm" id="priv-view-job">---</span>
                    <select class="privacy-badge edit-elm" id="priv-edit-job" style="display: none;">
                        <option value="public">Công khai</option><option value="friends">Bạn bè</option><option value="private">Chỉ mình tôi</option>
                    </select>
                </div>
            </div>

            <div class="data-row" id="row-faculty">
                <div class="col-label">Khoa (Hành chính)</div>
                <div class="col-value">
                    <span class="view-elm" id="w-faculty">---</span>
                    <select class="edit-select edit-elm" id="w-faculty-input" style="display: none;">
                        <option value="">-- Chọn Khoa của bạn --</option>
                        <option value="1">Khoa Ngân hàng</option><option value="2">Khoa Tài chính</option>
                        <option value="3">Khoa Kế toán - Kiểm toán</option><option value="4">Khoa Quản trị kinh doanh</option>
                        <option value="5">Khoa Hệ thống Thông tin</option><option value="6">Khoa Luật Kinh tế</option>
                    </select>
                </div>
                <div class="col-status">
                    <span class="privacy-badge view-elm" id="priv-view-faculty">---</span>
                    <select class="privacy-badge edit-elm" id="priv-edit-faculty" style="display: none;">
                        <option value="public">Công khai</option><option value="private">Chỉ mình tôi</option>
                    </select>
                </div>
            </div>

            <div class="data-row" id="row-class">
                <div class="col-label">Lớp sinh hoạt</div>
                <div class="col-value">
                    <span class="view-elm" id="w-class">---</span>
                    <input type="text" class="edit-input edit-elm" id="w-class-input" placeholder="VD: K27HTTTB" style="display: none;">
                </div>
                <div class="col-status">
                    <span class="privacy-badge view-elm" id="priv-view-class">---</span>
                    <select class="privacy-badge edit-elm" id="priv-edit-class" style="display: none;">
                        <option value="public">Công khai</option><option value="friends">Bạn bè</option><option value="private">Chỉ mình tôi</option>
                    </select>
                </div>
            </div>

            <div class="data-row" id="row-phone">
                <div class="col-label">Số điện thoại</div>
                <div class="col-value">
                    <span class="view-elm" id="w-phone">---</span>
                    <input type="text" class="edit-input edit-elm" id="w-phone-input" placeholder="Nhập SĐT của bạn" style="display: none;">
                </div>
                <div class="col-status">
                    <span class="privacy-badge view-elm" id="priv-view-phone">---</span>
                    <select class="privacy-badge edit-elm" id="priv-edit-phone" style="display: none;">
                        <option value="public">Công khai</option><option value="private">Chỉ mình tôi</option>
                    </select>
                </div>
            </div>

            <div class="data-row" id="row-gender">
                <div class="col-label">Giới tính</div>
                <div class="col-value">
                    <span class="view-elm" id="w-gender">---</span>
                    <select class="edit-select edit-elm" id="w-gender-input" style="display: none;">
                        <option value="">Chưa xác định</option><option value="Nam">Nam</option>
                        <option value="Nữ">Nữ</option><option value="Khác">Khác</option>
                    </select>
                </div>
                <div class="col-status">
                    <span class="privacy-badge view-elm" id="priv-view-gender">---</span>
                    <select class="privacy-badge edit-elm" id="priv-edit-gender" style="display: none;">
                        <option value="public">Công khai</option><option value="private">Chỉ mình tôi</option>
                    </select>
                </div>
            </div>

            <div class="data-row" id="row-dob">
                <div class="col-label">Ngày sinh</div>
                <div class="col-value">
                    <span class="view-elm" id="w-dob">---</span>
                    <input type="date" class="edit-input edit-elm" id="w-dob-input" style="display: none;">
                </div>
                <div class="col-status">
                    <span class="privacy-badge view-elm" id="priv-view-dob">---</span>
                    <select class="privacy-badge edit-elm" id="priv-edit-dob" style="display: none;">
                        <option value="public">Công khai</option><option value="friends">Bạn bè</option><option value="private">Chỉ mình tôi</option>
                    </select>
                </div>
            </div>

            <div class="data-row" id="row-social">
                <div class="col-label">Liên kết MXH</div>
                <div class="col-value">
                    <span class="view-elm" id="w-social">---</span>
                    <input type="text" class="edit-input edit-elm" id="w-social-input" placeholder="VD: github.com/username" style="display: none;">
                </div>
                <div class="col-status">
                    <span class="privacy-badge view-elm" id="priv-view-social">---</span>
                    <select class="privacy-badge edit-elm" id="priv-edit-social" style="display: none;">
                        <option value="public">Công khai</option><option value="friends">Bạn bè</option><option value="private">Chỉ mình tôi</option>
                    </select>
                </div>
            </div>

        </div>

        <div class="add-field-wrapper edit-elm" id="add-field-box">
            <select class="add-field-select" id="add-field-select">
                <option value="">-- Chọn trường muốn khai báo --</option>
            </select>
            <button type="button" onclick="addFieldToForm()" style="background: #e7f3ff; color: #1877f2; border: none; padding: 9px 15px; border-radius: 6px; font-weight: 600; cursor: pointer;">
                <i class="fa-solid fa-plus"></i> Thêm vào Hồ sơ
            </button>
        </div>

        <div class="action-buttons" id="wireframe-actions">
            <button class="btn-cancel" onclick="cancelEdit()">Hủy</button>
            <button class="btn-save" onclick="saveWireframeData()">Lưu hồ sơ</button>
        </div>
    </div>
</div>

<script>
    let isWireframeEditing = false;

    // Cấu hình các trường có thể Thêm/Ẩn
    const dynamicFields = {
        'bio': 'Tiểu sử', 'job': 'Công việc', 'faculty': 'Khoa (Hành chính)',
        'class': 'Lớp sinh hoạt', 'phone': 'Số điện thoại', 'gender': 'Giới tính',
        'dob': 'Ngày sinh', 'social': 'Liên kết MXH'
    };

    function renderPrivacyIcon(val) {
        if(val === 'public') return '<i class="fa-solid fa-earth-americas"></i> Công khai';
        if(val === 'friends') return '<i class="fa-solid fa-user-group"></i> Bạn bè';
        return '<i class="fa-solid fa-lock"></i> Chỉ mình tôi';
    }

    function fillWireframeData() {
        const user = window.currentProfileUser || {};
        const priv = user.privacy || {};
        
        document.getElementById('w-email').innerText = user.email || "---";
        document.getElementById('w-email-input').value = user.email || "";
        
        document.getElementById('w-msv').innerText = user.msv || "---";
        document.getElementById('w-msv-input').value = user.msv || "";
        
        document.getElementById('w-name').innerText = user.name || "---";
        document.getElementById('w-name-input').value = user.name || "";
        
        document.getElementById('w-bio').innerText = user.bio || "";
        document.getElementById('w-bio-input').value = user.bio || "";

        document.getElementById('w-job').innerText = user.job || "";
        document.getElementById('w-job-input').value = user.job || "";
        
        document.getElementById('w-faculty').innerText = user.faculty || "";
        if(user.faculty_id) document.getElementById('w-faculty-input').value = user.faculty_id;
        
        document.getElementById('w-class').innerText = user.className || "";
        document.getElementById('w-class-input').value = user.className || "";
        
        document.getElementById('w-phone').innerText = user.phone || "";
        document.getElementById('w-phone-input').value = user.phone || "";

        document.getElementById('w-gender').innerText = user.gender || "";
        document.getElementById('w-gender-input').value = user.gender || "";

        document.getElementById('w-dob').innerText = user.dob ? user.dob.split('-').reverse().join('/') : "";
        document.getElementById('w-dob-input').value = user.dob || "";

        document.getElementById('w-social').innerText = user.social_links || "";
        document.getElementById('w-social-input').value = user.social_links || "";

        const fields = ['name', 'bio', 'job', 'faculty', 'class', 'phone', 'gender', 'dob', 'social'];
        const dbFields = ['full_name', 'bio', 'job', 'faculty', 'class_name', 'phone', 'gender', 'dob', 'social_links'];
        
        fields.forEach((f, idx) => {
            const val = priv[dbFields[idx]] || 'private';
            document.getElementById('priv-view-' + f).innerHTML = renderPrivacyIcon(val);
            document.getElementById('priv-edit-' + f).value = val;
        });
    }

    // Logic Quản lý Ẩn/Hiện các trường chưa có data
    function processFieldVisibility() {
        const selectBox = document.getElementById('add-field-select');
        selectBox.innerHTML = '<option value="">-- Chọn trường muốn khai báo --</option>';

        Object.keys(dynamicFields).forEach(key => {
            const row = document.getElementById('row-' + key);
            const val = document.getElementById('w-' + key + '-input').value;
            
            if (val && val.trim() !== '') {
                row.style.display = 'flex';
            } else {
                row.style.display = 'none'; // Ẩn trường trống
                // Đưa vào Dropdown để thêm sau
                const opt = document.createElement('option');
                opt.value = key; opt.innerText = dynamicFields[key];
                selectBox.appendChild(opt);
            }
        });

        // 3 Trường bắt buộc lúc nào cũng hiện
        document.getElementById('row-email').style.display = 'flex';
        document.getElementById('row-msv').style.display = 'flex';
        document.getElementById('row-name').style.display = 'flex';
    }

    // Khi User bấm nút Thêm trường
    window.addFieldToForm = function() {
        const selectBox = document.getElementById('add-field-select');
        const key = selectBox.value;
        if (!key) return;
        document.getElementById('row-' + key).style.display = 'flex';
        selectBox.querySelector(`option[value="${key}"]`).remove();
    }

    window.toggleEditWireframe = function() {
        isWireframeEditing = !isWireframeEditing;
        
        const viewElms = document.querySelectorAll('.view-elm');
        const editElms = document.querySelectorAll('.edit-elm');
        const actionBox = document.getElementById('wireframe-actions');
        const editBtn = document.querySelector('.btn-toggle-edit');

        viewElms.forEach(el => el.style.display = isWireframeEditing ? 'none' : 'inline-flex');
        editElms.forEach(el => el.style.display = isWireframeEditing ? 'block' : 'none');
        
        actionBox.style.display = isWireframeEditing ? 'flex' : 'none';
        editBtn.style.display = isWireframeEditing ? 'none' : 'block';
    };

    window.cancelEdit = function() {
        fillWireframeData();
        processFieldVisibility();
        toggleEditWireframe();
    };

    // Khởi chạy khi load tab
    fillWireframeData();
    processFieldVisibility();

    // LƯU DATA & CẬP NHẬT HEADER TRỰC TIẾP
    // LƯU DATA & CẬP NHẬT HEADER TRỰC TIẾP (CHUẨN LARAVEL PHP)
    window.saveWireframeData = async function() {
        const user = window.currentProfileUser || {};
        const studentCode = user.msv;

        // Bỏ _token ở body đi, vì Laravel chuộng đọc từ Header đối với JSON
        const payload = {
            full_name: document.getElementById('w-name-input').value,
            bio: document.getElementById('w-bio-input').value,
            job: document.getElementById('w-job-input').value,
            faculty_id: document.getElementById('w-faculty-input').value,
            class_name: document.getElementById('w-class-input').value,
            phone: document.getElementById('w-phone-input').value,
            gender: document.getElementById('w-gender-input').value,
            dob: document.getElementById('w-dob-input').value,
            social_links: document.getElementById('w-social-input').value,
            privacy_settings: {
                full_name: document.getElementById('priv-edit-name').value,
                bio: document.getElementById('priv-edit-bio').value,
                job: document.getElementById('priv-edit-job').value,
                faculty: document.getElementById('priv-edit-faculty').value,
                class_name: document.getElementById('priv-edit-class').value,
                phone: document.getElementById('priv-edit-phone').value,
                gender: document.getElementById('priv-edit-gender').value,
                dob: document.getElementById('priv-edit-dob').value,
                social_links: document.getElementById('priv-edit-social').value
            }
        };

        const saveBtn = document.querySelector('.btn-save');
        saveBtn.innerText = "Đang lưu..."; saveBtn.disabled = true;

        try {
            // Sửa URL bỏ chữ /api/ đi để khớp với routes/web.php
            const response = await fetch(`/profile/${studentCode}`, {
                method: 'PUT',
                headers: { 
                    'Content-Type': 'application/json', 
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}' // BẮT BUỘC CÓ DÒNG NÀY ĐỂ LARAVEL KHÔNG TỪ CHỐI
                },
                body: JSON.stringify(payload)
            });

            const result = await response.json();

            if(response.ok) {
                // 1. Cập nhật Biến toàn cục
                window.currentProfileUser = { ...user, ...payload, 
                    name: payload.full_name, 
                    social: payload.social_links, 
                    privacy: payload.privacy_settings 
                };

                const facSel = document.getElementById('w-faculty-input');
                if(facSel.selectedIndex > 0) window.currentProfileUser.faculty = facSel.options[facSel.selectedIndex].text;

                // 2. Cập nhật Giao diện Tab
                fillWireframeData();
                processFieldVisibility();
                toggleEditWireframe(); 

                // 3. Cập nhật DOM trên Header Real-time
                document.getElementById('p-name').innerText = payload.full_name || '---';
                document.getElementById('p-bio').innerText = payload.bio || 'Chưa có tiểu sử';
                
                const linkEl = document.getElementById('p-link');
                if(payload.social_links) {
                    linkEl.innerText = payload.social_links;
                    linkEl.href = payload.social_links.startsWith('http') ? payload.social_links : 'https://' + payload.social_links;
                    linkEl.style.display = 'block';
                } else {
                    linkEl.style.display = 'none';
                }

                alert("Đã đồng bộ hồ sơ lên Database thành công!");
            } else {
                // Laravel sẽ trả về lỗi 422 nếu Validation bị sai (VD: tên quá dài, trống...)
                console.error("Lỗi Validation từ Backend:", result);
                alert("Hệ thống từ chối: " + (result.message || "Kiểm tra lại dữ liệu nhập."));
            }
        } catch (error) {
            console.error(error); alert("Lỗi kết nối máy chủ PHP.");
        } finally {
            saveBtn.innerText = "Lưu hồ sơ"; saveBtn.disabled = false;
        }
    };
</script>