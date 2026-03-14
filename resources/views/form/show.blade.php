<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $form->title }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

        body { 
            font-family: 'Inter', sans-serif; 
            background-color: #f3f4f6; 
            min-height: 100vh; 
            display: flex; 
            justify-content: center; 
            margin: 0; 
            padding: 40px 20px; 
            box-sizing: border-box;
            color: #1f2937;
        }
        
        .main-container { width: 100%; max-width: 650px; }

        .form-card {
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -1px rgba(0,0,0,0.06);
            overflow: hidden;
        }

        .card-header {
            background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%);
            color: white; padding: 40px 30px; text-align: center;
        }

        .card-header h1 { margin: 0 0 10px 0; font-size: 26px; font-weight: 700; }
        .card-header p { margin: 0; font-size: 15px; opacity: 0.9; line-height: 1.5; white-space: pre-wrap; }

        .card-body { padding: 40px 30px; }

        .input-group { margin-bottom: 24px; }
        .input-label { display: block; font-weight: 600; margin-bottom: 8px; font-size: 14px; color: #374151; }
        .required-asterisk { color: #ef4444; margin-left: 2px; }

        .form-control { 
            width: 100%; padding: 12px 16px; 
            border: 1px solid #d1d5db; border-radius: 8px; 
            background: #f9fafb; font-family: inherit; font-size: 15px; color: #1f2937;
            transition: all 0.2s; box-sizing: border-box;
        }
        .form-control:focus { 
            outline: none; border-color: #4f46e5; background: #fff;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }

        textarea.form-control { resize: vertical; min-height: 80px; }

        /* KHU VỰC KÉO THẢ TẢI FILE (ĐÃ SỬA) */
        .file-drop-area {
            position: relative; 
            display: flex; flex-direction: column; align-items: center; justify-content: center;
            padding: 30px 20px; border: 2px dashed #cbd5e1; border-radius: 12px;
            background: #f8fafc; cursor: pointer; transition: 0.2s; text-align: center;
            min-height: 150px; /* Force chiều cao tối thiểu để khung không bị móp */
        }
        .file-drop-area:hover { border-color: #4f46e5; background: #eef2ff; }
        
        .file-drop-area input[type="file"] {
            position: absolute; top: 0; left: 0; width: 100%; height: 100%;
            opacity: 0; cursor: pointer; z-index: 2;
        }
        
        .upload-content {
            display: flex; flex-direction: column; align-items: center;
        }
        .file-icon { font-size: 40px; color: #94a3b8; margin-bottom: 12px; transition: 0.2s; }
        .file-drop-area:hover .file-icon { color: #4f46e5; transform: translateY(-3px); }
        .file-text { font-size: 14px; color: #64748b; font-weight: 500; }
        .file-text span { color: #4f46e5; text-decoration: underline; }

        /* KHU VỰC HIỂN THỊ ẢNH PREVIEW (ĐÃ FIX LỖI TRÀN ẢNH) */
        .preview-container {
            display: none; /* Ẩn mặc định */
            width: 100%;
            flex-direction: column; 
            align-items: center; 
            justify-content: center;
            position: relative; /* Nằm bình thường trong khung, đẩy khung giãn ra */
            z-index: 5; /* Cao hơn thẻ input để bấm được nút Hủy */
        }
        .preview-container img {
            max-width: 100%;
            max-height: 250px; /* Giới hạn để ảnh không quá to */
            object-fit: contain;
            border-radius: 8px;
            border: 1px solid #cbd5e1;
            padding: 4px;
            background: #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            margin-bottom: 15px; /* Tạo khoảng cách với nút Hủy bên dưới */
        }
        .btn-remove-file {
            background: #fef2f2; border: 1px solid #fecaca; color: #dc2626;
            padding: 8px 16px; border-radius: 6px; font-size: 13px; font-weight: 600; 
            cursor: pointer; transition: 0.2s; display: flex; align-items: center; gap: 5px; 
            position: relative; z-index: 10; /* Đảm bảo luôn bấm được */
        }
        .btn-remove-file:hover { background: #fee2e2; }

        /* CÁC NÚT & THÔNG BÁO */
        .btn-submit {
            width: 100%; background: #4f46e5; color: white; padding: 14px;
            border: none; border-radius: 8px; cursor: pointer; font-size: 16px; font-weight: 600;
            transition: 0.2s; margin-top: 10px; display: flex; justify-content: center; align-items: center; gap: 8px;
        }
        .btn-submit:hover { background: #4338ca; transform: translateY(-1px); box-shadow: 0 4px 6px rgba(67, 56, 202, 0.2); }

        .text-danger { color: #ef4444; font-size: 13px; margin-top: 6px; display: flex; align-items: center; gap: 4px; font-weight: 500;}
        .alert-success { background: #d1fae5; color: #065f46; padding: 16px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #a7f3d0; font-weight: 500; text-align: center; }
        .alert-error { background: #fee2e2; color: #991b1b; padding: 16px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #fecaca; font-size: 14px;}
        
        .personal-info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        @media (max-width: 500px) { .personal-info-grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body>

@php
    $testUser = auth()->user() ?? \App\Models\User::first();
@endphp

<div class="main-container">
    @if(session('success'))
        <div class="alert-success"><i class="fa-solid fa-circle-check"></i> {{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert-error">
            <i class="fa-solid fa-triangle-exclamation"></i> Vui lòng kiểm tra và điền đầy đủ các trường thông tin bắt buộc.
        </div>
    @endif

    <div class="form-card">
        <div class="card-header">
            <h1>{{ $form->title }}</h1>
            @if($form->description)
                <p>{{ $form->description }}</p>
            @endif
        </div>

        <div class="card-body">
            <form action="{{ route('forms.submit', $form->id) }}" method="POST" enctype="multipart/form-data">
                @csrf 

                <div style="margin-bottom: 30px; padding-bottom: 25px; border-bottom: 1px solid #e5e7eb;">
                    <h3 style="font-size: 16px; margin-top: 0; margin-bottom: 15px; color: #4f46e5;">
                        <i class="fa-solid fa-address-card"></i> Thông tin Sinh viên
                    </h3>

                    <div class="personal-info-grid">
                        <div class="input-group" style="margin-bottom: 0;">
                            <label class="input-label">Họ và tên <span class="required-asterisk">*</span></label>
                            <input type="text" name="student_name" class="form-control" value="{{ old('student_name', $testUser->display_name ?? '') }}" placeholder="VD: Nguyễn Văn A" required oninvalid="this.setCustomValidity('Vui lòng nhập đầy đủ tên của bạn')" oninput="this.setCustomValidity('')">
                            @error('student_name') <div class="text-danger">{{ $message }}</div> @enderror
                        </div>

                        <div class="input-group" style="margin-bottom: 0;">
                            <label class="input-label">Mã sinh viên <span class="required-asterisk">*</span></label>
                            <input type="text" name="student_code" class="form-control" value="{{ old('student_code', $testUser->student_code ?? '') }}" placeholder="VD: 202416674" required oninvalid="this.setCustomValidity('Vui lòng nhập Mã sinh viên của bạn')" oninput="this.setCustomValidity('')">
                            @error('student_code') <div class="text-danger">{{ $message }}</div> @enderror
                        </div>

                        <div class="input-group" style="margin-bottom: 0; grid-column: 1 / -1;">
                            <label class="input-label">Email trường <span class="required-asterisk">*</span></label>
                            <input type="email" name="student_email" class="form-control" value="{{ old('student_email', $testUser->email ?? '') }}" placeholder="VD: sv001@hvnh.edu.vn" required oninvalid="this.setCustomValidity('Vui lòng nhập email trường hợp lệ')" oninput="this.setCustomValidity('')">
                            @error('student_email') <div class="text-danger">{{ $message }}</div> @enderror
                        </div>

                        <div class="input-group" style="margin-bottom: 0;">
                            <label class="input-label">Trường <span class="required-asterisk">*</span></label>
                            <input type="text" name="school" class="form-control" value="{{ old('school', $testUser->school ?? '') }}" placeholder="VD: Học viện Ngân hàng" required oninvalid="this.setCustomValidity('Vui lòng nhập tên Trường của bạn')" oninput="this.setCustomValidity('')">
                            @error('school') <div class="text-danger">{{ $message }}</div> @enderror
                        </div>

                        <div class="input-group" style="margin-bottom: 0;">
                            <label class="input-label">Lớp <span class="required-asterisk">*</span></label>
                            <input type="text" name="class_name" class="form-control" value="{{ old('class_name', $testUser->class_name ?? '') }}" placeholder="VD: K24 CNTT" required oninvalid="this.setCustomValidity('Vui lòng nhập Lớp của bạn')" oninput="this.setCustomValidity('')">
                            @error('class_name') <div class="text-danger">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    @if(!$testUser)
                        <div style="margin-top: 15px; font-size: 13px; color: #d93025; background: #fef2f2; padding: 10px; border-radius: 6px; border: 1px solid #fecaca;">
                            <i class="fa-solid fa-triangle-exclamation"></i> <strong>Lưu ý:</strong> Bạn đang thao tác ở chế độ khách. Vui lòng tự điền đầy đủ các thông tin trên để được ghi nhận minh chứng.
                        </div>
                    @else
                        <div style="margin-top: 15px; font-size: 13px; color: #166534; background: #f0fdf4; padding: 10px; border-radius: 6px; border: 1px solid #bbf7d0;">
                            <i class="fa-solid fa-circle-check"></i> Hệ thống đã tự động điền các thông tin có sẵn từ tài khoản của bạn. Vui lòng bổ sung các thông tin còn trống.
                        </div>
                    @endif
                </div>

                <h3 style="font-size: 16px; margin-top: 0; margin-bottom: 15px; color: #4f46e5;">
                    <i class="fa-solid fa-list-check"></i> Thông tin Minh chứng
                </h3>

                @foreach($form->fields as $field)
                    @php $fieldName = 'field_' . $field->id; @endphp
                    
                    <div class="input-group">
                        <label class="input-label" for="{{ $fieldName }}">
                            {{ $field->label }} 
                            @if($field->required) <span class="required-asterisk">*</span> @endif
                        </label>

                        @if($field->type === 'textarea')
                            <textarea name="{{ $fieldName }}" id="{{ $fieldName }}" class="form-control" placeholder="Nhập câu trả lời của bạn..." rows="3" {{ $field->required ? 'required' : '' }} oninvalid="this.setCustomValidity('Vui lòng điền thông tin vào đây')" oninput="this.setCustomValidity('')">{{ old($fieldName) }}</textarea>
                        
                        @elseif($field->type === 'select')
                            <select name="{{ $fieldName }}" id="{{ $fieldName }}" class="form-control" {{ $field->required ? 'required' : '' }} oninvalid="this.setCustomValidity('Vui lòng chọn một tùy chọn')" onchange="this.setCustomValidity('')">
                                <option value="">-- Vui lòng chọn --</option>
                                @if($field->options)
                                    @foreach($field->options as $option)
                                        <option value="{{ $option }}" {{ old($fieldName) == $option ? 'selected' : '' }}>
                                            {{ $option }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        
                        @elseif($field->type === 'file')
                            <div class="file-drop-area">
                                <input type="file" name="{{ $fieldName }}" id="{{ $fieldName }}" accept="image/*" class="file-input" {{ $field->required ? 'required' : '' }} oninvalid="this.setCustomValidity('Vui lòng tải lên ảnh minh chứng của bạn')" onchange="this.setCustomValidity('')">
                                
                                <div class="upload-content">
                                    <i class="fa-solid fa-cloud-arrow-up file-icon"></i>
                                    <div class="file-text">Kéo thả ảnh minh chứng vào đây hoặc <span>bấm để chọn ảnh</span></div>
                                    <div style="font-size: 12px; color: #94a3b8; margin-top: 8px;">(Chỉ hỗ trợ định dạng ảnh JPG, PNG... - Tối đa 5MB)</div>
                                </div>

                                <div class="preview-container">
                                    <img>
                                    <button type="button" class="btn-remove-file">
                                        <i class="fa-solid fa-xmark"></i> Hủy
                                    </button>
                                </div>
                            </div>
                        
                        @else
                            <input type="{{ $field->type === 'phone' ? 'tel' : ($field->type === 'email' ? 'email' : 'text') }}" 
                                   name="{{ $fieldName }}" 
                                   id="{{ $fieldName }}" 
                                   class="form-control"
                                   value="{{ old($fieldName) }}"
                                   placeholder="Nhập thông tin..."
                                   {{ $field->required ? 'required' : '' }}
                                   oninvalid="this.setCustomValidity('Vui lòng điền thông tin vào đây')" 
                                   oninput="this.setCustomValidity('')">
                        @endif

                        @error($fieldName)
                            <div class="text-danger"><i class="fa-solid fa-circle-exclamation"></i> Vui lòng hoàn thành trường này.</div>
                        @enderror
                    </div>
                @endforeach

                <button type="submit" class="btn-submit">
                    <i class="fa-solid fa-paper-plane"></i> Gửi minh chứng
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    document.querySelectorAll('.file-drop-area').forEach(dropArea => {
        const fileInput = dropArea.querySelector('.file-input');
        const uploadContent = dropArea.querySelector('.upload-content');
        const previewContainer = dropArea.querySelector('.preview-container');
        const previewImage = previewContainer.querySelector('img');
        const removeFileBtn = previewContainer.querySelector('.btn-remove-file');

        if (fileInput) {
            fileInput.addEventListener('change', function(event) {
                const file = event.target.files[0];
                this.setCustomValidity(''); 
                
                if (file && file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        previewImage.src = e.target.result;
                        uploadContent.style.display = 'none';
                        previewContainer.style.display = 'flex'; // Hiện ảnh lên
                    }
                    reader.readAsDataURL(file);
                } else {
                    resetPreview();
                }
            });
        }

        if (removeFileBtn) {
            removeFileBtn.addEventListener('click', function(event) {
                event.preventDefault(); // Chặn hành vi mặc định
                event.stopPropagation(); // Chặn việc click xuyên qua làm mở bảng chọn file
                resetPreview();
            });
        }

        function resetPreview() {
            if (fileInput) fileInput.value = ""; 
            previewImage.src = "";
            previewContainer.style.display = 'none'; // Giấu ảnh đi
            uploadContent.style.display = 'flex'; // Trả lại cái đám mây
        }
    });
</script>

</body>
</html>