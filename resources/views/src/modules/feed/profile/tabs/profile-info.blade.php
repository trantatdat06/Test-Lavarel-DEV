<style>
    /* CSS nguyên bản */
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

    /* Thêm field */
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
    
    /* Hiệu ứng mượt khi chuyển đổi filter */
    .data-row { transition: all 0.3s ease; }
    .data-row.hidden-field { display: none !important; }
</style>

@php
    $priv = json_decode($user->privacy_settings ?? '{}', true);
    
    function renderPrivacyIcon($val) {
        if($val === 'public') return '<i class="fa-solid fa-earth-americas"></i> Công khai';
        if($val === 'friends') return '<i class="fa-solid fa-user-group"></i> Bạn bè';
        return '<i class="fa-solid fa-lock"></i> Chỉ mình tôi';
    }
    
    // Cấu trúc lại danh mục dữ liệu
    $fieldConfig = [
        'basic' => [
            'full_name' => ['label' => 'Họ và tên hiển thị', 'val' => $user->full_name],
            'bio' => ['label' => 'Tiểu sử', 'val' => $user->bio],
            'phone' => ['label' => 'Số điện thoại', 'val' => $user->phone],
            'gender' => ['label' => 'Giới tính', 'val' => $user->gender],
            'dob' => ['label' => 'Ngày sinh', 'val' => $user->dob],
        ],
        'work' => [
            'job' => ['label' => 'Công việc', 'val' => $user->job],
            'faculty_id' => ['label' => 'Khoa (Hành chính)', 'val' => $user->faculty_id],
            'class_name' => ['label' => 'Lớp sinh hoạt', 'val' => $user->class_name],
        ],
        'social' => [
            'social_links' => ['label' => 'Liên kết MXH (URL)', 'val' => $user->social_links]
        ]
    ];
@endphp

<div class="info-wireframe-layout">
    <div class="info-sidebar">
        <div class="info-sidebar-title">Hồ sơ cá nhân</div>
        <div class="info-menu-item active" data-cat="basic" onclick="window.filterInfoCategory('basic', this)">
            <i class="fa-solid fa-address-card"></i> Thông tin cơ bản
        </div>
        <div class="info-menu-item" data-cat="work" onclick="window.filterInfoCategory('work', this)">
            <i class="fa-solid fa-briefcase"></i> Công việc & Học vấn
        </div>
        <div class="info-menu-item" data-cat="social" onclick="window.filterInfoCategory('social', this)">
            <i class="fa-solid fa-link"></i> Liên kết MXH
        </div>
    </div>

    <div class="info-content-area" id="info-form-container">
        <div class="info-header-action">
            <div class="info-section-title" id="current-cat-title">Thông tin cơ bản</div>
            <button class="btn-toggle-edit" id="btn-edit-toggle" onclick="window.toggleWireframeUI()">
                <i class="fa-solid fa-pen"></i> Chỉnh sửa hồ sơ
            </button>
        </div>

        <div class="data-table-wrapper" id="dynamic-fields-container">
            <div class="data-row" style="padding-top: 0; padding-bottom: 10px; border-bottom: 2px solid #f0f2f5;">
                <div class="col-label" style="color: #1c1e21; font-size: 13px;">TRƯỜNG DỮ LIỆU</div>
                <div class="col-value" style="color: #1c1e21; font-size: 13px;">NỘI DUNG</div>
                <div class="col-status" style="color: #1c1e21; font-size: 13px; justify-content: center;">BẢO MẬT</div>
            </div>

            <div class="data-row info-cat-basic">
                <div class="col-label">Email trường</div>
                <div class="col-value">
                    <span class="view-elm">{{ $user->email ?? '---' }}</span>
                    <input type="text" class="edit-input edit-elm input-readonly" value="{{ $user->email }}" disabled style="display: none;">
                </div>
                <div class="col-status"><button class="privacy-badge" disabled><i class="fa-solid fa-lock"></i> Mặc định</button></div>
            </div>

            <div class="data-row info-cat-basic">
                <div class="col-label">Mã sinh viên</div>
                <div class="col-value">
                    <span class="view-elm">{{ $user->student_code ?? '---' }}</span>
                    <input type="text" class="edit-input edit-elm input-readonly" value="{{ $user->student_code }}" disabled style="display: none;">
                </div>
                <div class="col-status"><button class="privacy-badge" disabled><i class="fa-solid fa-lock"></i> Mặc định</button></div>
            </div>

            @foreach($fieldConfig as $catName => $fields)
                @foreach($fields as $key => $field)
                    <div class="data-row info-cat-{{ $catName }} {{ $catName !== 'basic' ? 'hidden-field' : '' }}" 
                         id="row-{{ $key }}" 
                         style="display: {{ (empty($field['val']) && $key !== 'full_name') ? 'none' : 'flex' }};">
                        
                        <div class="col-label">{{ $field['label'] }}</div>
                        <div class="col-value">
                            @if($key === 'faculty_id')
                                <span class="view-elm">{{ $user->faculty->name ?? '---' }}</span>
                                <select class="edit-select edit-elm" id="w-{{ $key }}-input" style="display: none;">
                                    <option value="">-- Chọn Khoa của bạn --</option>
                                    @foreach($faculties as $fac)
                                        <option value="{{ $fac->id }}" {{ $user->faculty_id == $fac->id ? 'selected' : '' }}>{{ $fac->name }}</option>
                                    @endforeach
                                </select>
                            @elseif($key === 'gender')
                                <span class="view-elm">{{ $field['val'] ?? '---' }}</span>
                                <select class="edit-select edit-elm" id="w-{{ $key }}-input" style="display: none;">
                                    <option value="">Chưa xác định</option>
                                    <option value="Nam" {{ $field['val'] == 'Nam' ? 'selected' : '' }}>Nam</option>
                                    <option value="Nữ" {{ $field['val'] == 'Nữ' ? 'selected' : '' }}>Nữ</option>
                                    <option value="Khác" {{ $field['val'] == 'Khác' ? 'selected' : '' }}>Khác</option>
                                </select>
                            @elseif($key === 'dob')
                                <span class="view-elm">{{ empty($field['val']) ? '---' : date('d/m/Y', strtotime($field['val'])) }}</span>
                                <input type="date" class="edit-input edit-elm" id="w-{{ $key }}-input" value="{{ $field['val'] }}" style="display: none;">
                            @else
                                <span class="view-elm">{{ $field['val'] ?? '---' }}</span>
                                <input type="text" class="edit-input edit-elm" id="w-{{ $key }}-input" value="{{ $field['val'] }}" style="display: none;">
                            @endif
                        </div>
                        <div class="col-status">
                            <span class="privacy-badge view-elm">{!! renderPrivacyIcon($priv[$key] ?? 'public') !!}</span>
                            <select class="privacy-badge edit-elm" id="priv-{{ $key }}" style="display: none;">
                                <option value="public" {{ ($priv[$key] ?? '') == 'public' ? 'selected' : '' }}>Công khai</option>
                                <option value="friends" {{ ($priv[$key] ?? '') == 'friends' ? 'selected' : '' }}>Bạn bè</option>
                                <option value="private" {{ ($priv[$key] ?? '') == 'private' ? 'selected' : '' }}>Chỉ mình tôi</option>
                            </select>
                        </div>
                    </div>
                @endforeach
            @endforeach
        </div>

        <div class="add-field-wrapper edit-elm" id="add-field-box">
            <select class="add-field-select" id="add-field-select">
                <option value="">-- Khai báo thêm thông tin --</option>
                </select>
            <button type="button" onclick="window.addNewFieldToForm()" style="background: #e7f3ff; color: #1877f2; border: none; padding: 9px 15px; border-radius: 6px; font-weight: 600; cursor: pointer;">
                <i class="fa-solid fa-plus"></i> Thêm vào Hồ sơ
            </button>
        </div>

        <div class="action-buttons" id="wireframe-actions">
            <button class="btn-cancel" onclick="window.toggleWireframeUI()">Hủy</button>
            <button class="btn-save" onclick="window.saveWireframeProfileData(this)">Lưu hồ sơ</button>
        </div>
    </div>
</div>

<script>
    // Định nghĩa các trường theo danh mục cho JS sử dụng
    window.infoCategories = {!! json_encode($fieldConfig) !!};
    window.currentCat = 'basic';

    // Hàm Filter Danh mục
    window.filterInfoCategory = function(cat, element) {
        window.currentCat = cat;
        
        // 1. Cập nhật UI Menu
        document.querySelectorAll('.info-menu-item').forEach(el => el.classList.remove('active'));
        element.classList.add('active');
        
        // 2. Cập nhật Tiêu đề
        document.getElementById('current-cat-title').innerText = element.innerText.trim();
        
        // 3. Ẩn/Hiện các dòng dữ liệu
        document.querySelectorAll('.data-row[class*="info-cat-"]').forEach(row => {
            if (row.classList.contains('info-cat-' + cat)) {
                row.classList.remove('hidden-field');
            } else {
                row.classList.add('hidden-field');
            }
        });

        // 4. Cập nhật danh sách "Thêm trường" cho đúng danh mục
        updateAddFieldDropdown();
    };

    function updateAddFieldDropdown() {
        const select = document.getElementById('add-field-select');
        const fields = window.infoCategories[window.currentCat];
        let html = '<option value="">-- Khai báo thêm thông tin --</option>';
        
        for (const key in fields) {
            const row = document.getElementById('row-' + key);
            // Chỉ hiện những trường đang bị ẩn hoàn toàn (display: none)
            if (row && row.style.display === 'none') {
                html += `<option value="${key}">${fields[key].label}</option>`;
            }
        }
        select.innerHTML = html;
    }

    window.toggleWireframeUI = function() {
        window.isWireframeEditing = !window.isWireframeEditing;
        
        // Lấy tất cả view-elm và edit-elm trong danh mục hiện tại
        const rows = document.querySelectorAll('.info-cat-' + window.currentCat);
        
        rows.forEach(row => {
            row.querySelectorAll('.view-elm').forEach(el => el.style.display = window.isWireframeEditing ? 'none' : 'inline-flex');
            row.querySelectorAll('.edit-elm').forEach(el => el.style.display = window.isWireframeEditing ? 'block' : 'none');
        });

        document.getElementById('wireframe-actions').style.display = window.isWireframeEditing ? 'flex' : 'none';
        document.getElementById('btn-edit-toggle').style.display = window.isWireframeEditing ? 'none' : 'block';
        
        if(window.isWireframeEditing) updateAddFieldDropdown();
        document.getElementById('add-field-box').style.display = window.isWireframeEditing ? 'block' : 'none';
    };

    window.addNewFieldToForm = function() {
        const select = document.getElementById('add-field-select');
        const key = select.value;
        if(!key) return;
        
        const targetRow = document.getElementById('row-' + key);
        if(targetRow) {
            targetRow.style.display = 'flex';
            // Hiện luôn phần edit cho trường vừa thêm
            targetRow.querySelectorAll('.view-elm').forEach(el => el.style.display = 'none');
            targetRow.querySelectorAll('.edit-elm').forEach(el => el.style.display = 'block');
        }
        updateAddFieldDropdown();
    };

    window.saveWireframeProfileData = async function(btn) {
        btn.innerText = "Đang lưu..."; btn.disabled = true;
        const getVal = (id) => document.getElementById(id) ? document.getElementById(id).value : '';

        // Thu thập tất cả các trường (không chỉ trường đang hiện)
        const payload = {
            full_name: getVal('w-full_name-input'),
            bio: getVal('w-bio-input'),
            job: getVal('w-job-input'),
            faculty_id: getVal('w-faculty_id-input'),
            class_name: getVal('w-class_name-input'),
            phone: getVal('w-phone-input'),
            gender: getVal('w-gender-input'),
            dob: getVal('w-dob-input'),
            social_links: getVal('w-social_links-input'),
            privacy_settings: JSON.stringify({
                full_name: getVal('priv-full_name'),
                bio: getVal('priv-bio'),
                job: getVal('priv-job'),
                faculty_id: getVal('priv-faculty_id'),
                class_name: getVal('priv-class_name'),
                phone: getVal('priv-phone'),
                gender: getVal('priv-gender'),
                dob: getVal('priv-dob'),
                social_links: getVal('priv-social_links')
            })
        };

        try {
            const response = await fetch(`/profile/${window.studentCode}`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify(payload)
            });
            if(response.ok) {
                alert('Cập nhật hồ sơ thành công!');
                window.location.reload();
            } else { alert('Lỗi khi lưu dữ liệu.'); }
        } catch (err) { alert('Lỗi kết nối máy chủ.'); }
        finally { btn.innerText = "Lưu hồ sơ"; btn.disabled = false; }
    };

    // Khởi tạo trạng thái ban đầu
    updateAddFieldDropdown();
</script>