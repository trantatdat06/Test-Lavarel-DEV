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
            background-color: #f3f4f6; /* Nền xám nhạt hiện đại */
            min-height: 100vh; 
            display: flex; 
            justify-content: center; 
            margin: 0; 
            padding: 40px 20px; 
            box-sizing: border-box;
            color: #1f2937;
        }
        
        .main-container { 
            width: 100%; 
            max-width: 650px; 
        }

        /* Thẻ bao bọc toàn bộ form */
        .form-card {
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -1px rgba(0,0,0,0.06);
            overflow: hidden;
        }

        /* Banner Header */
        .card-header {
            background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%); /* Màu chàm Gradient */
            color: white;
            padding: 40px 30px;
            text-align: center;
        }

        .card-header h1 { margin: 0 0 10px 0; font-size: 26px; font-weight: 700; }
        .card-header p { margin: 0; font-size: 15px; opacity: 0.9; line-height: 1.5; white-space: pre-wrap; }

        .card-body { padding: 40px 30px; }

        /* Khối Thông tin Sinh viên (Auto-fill) */
        .student-profile-badge {
            display: flex;
            align-items: center;
            gap: 15px;
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            padding: 16px 20px;
            border-radius: 12px;
            margin-bottom: 30px;
            position: relative;
        }
        
        .avatar-circle {
            width: 50px; height: 50px;
            background: #dcfce7; color: #166534;
            border-radius: 50%; display: flex; align-items: center; justify-content: center;
            font-size: 20px; font-weight: bold; flex-shrink: 0;
        }

        .student-info h4 { margin: 0 0 4px 0; font-size: 16px; color: #166534; }
        .student-info p { margin: 0; font-size: 13px; color: #15803d; }
        .verified-tick { position: absolute; top: 16px; right: 20px; color: #10b981; font-size: 18px; }

        /* Các ô nhập liệu thông thường */
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

        /* Khu vực Tải file (Nét đứt) */
        .file-drop-area {
            position: relative;
            display: flex; flex-direction: column; align-items: center; justify-content: center;
            padding: 40px 20px; border: 2px dashed #cbd5e1; border-radius: 12px;
            background: #f8fafc; cursor: pointer; transition: 0.2s; text-align: center;
        }
        .file-drop-area:hover { border-color: #4f46e5; background: #eef2ff; }
        .file-drop-area input[type="file"] {
            position: absolute; top: 0; left: 0; width: 100%; height: 100%;
            opacity: 0; cursor: pointer;
        }
        .file-icon { font-size: 40px; color: #94a3b8; margin-bottom: 12px; transition: 0.2s; }
        .file-drop-area:hover .file-icon { color: #4f46e5; transform: translateY(-3px); }
        .file-text { font-size: 14px; color: #64748b; font-weight: 500; }
        .file-text span { color: #4f46e5; text-decoration: underline; }

        /* Nút Submit */
        .btn-submit {
            width: 100%; background: #4f46e5; color: white; padding: 14px;
            border: none; border-radius: 8px; cursor: pointer; font-size: 16px; font-weight: 600;
            transition: 0.2s; margin-top: 10px; display: flex; justify-content: center; align-items: center; gap: 8px;
        }
        .btn-submit:hover { background: #4338ca; transform: translateY(-1px); box-shadow: 0 4px 6px rgba(67, 56, 202, 0.2); }

        .text-danger { color: #ef4444; font-size: 13px; margin-top: 6px; display: flex; align-items: center; gap: 4px; font-weight: 500;}
        .alert-success { background: #d1fae5; color: #065f46; padding: 16px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #a7f3d0; font-weight: 500; text-align: center; }
        .alert-warning { background: #fef3c7; color: #92400e; padding: 16px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #fde68a; font-size: 14px; text-align: center;}
    </style>
</head>
<body>

@php
    // Lấy User test
    $testUser = auth()->user() ?? \App\Models\User::first();
@endphp

<div class="main-container">
    @if(session('success'))
        <div class="alert-success"><i class="fa-solid fa-circle-check"></i> {{ session('success') }}</div>
    @endif

    <div class="form-card">
        <div class="card-header">
            <h1>{{ $form->title }}</h1>
            @if($form->description)
                <p>{{ $form->description }}</p>
            @endif
        </div>

        <div class="card-body">
            @if($testUser)
                <div class="student-profile-badge">
                    <i class="fa-solid fa-circle-check verified-tick" title="Đã xác thực"></i>
                    <div class="avatar-circle">
                        <i class="fa-solid fa-user-graduate"></i>
                    </div>
                    <div class="student-info">
                        <h4>{{ $testUser->display_name }} ({{ $testUser->student_code ?? 'N/A' }})</h4>
                        <p><i class="fa-regular fa-envelope"></i> {{ $testUser->email }}</p>
                        <p style="font-size: 11px; margin-top: 4px; opacity: 0.8;">* Hệ thống tự động ghi nhận để cộng điểm rèn luyện.</p>
                    </div>
                </div>
            @else
                <div class="alert-warning">
                    <i class="fa-solid fa-triangle-exclamation"></i> Chế độ khách. Vui lòng đăng nhập để hệ thống lưu thông tin của bạn!
                </div>
            @endif

            <form action="{{ route('forms.submit', $form->id) }}" method="POST" enctype="multipart/form-data">
                @csrf 

                @foreach($form->fields as $field)
                    @php $fieldName = 'field_' . $field->id; @endphp
                    
                    <div class="input-group">
                        <label class="input-label" for="{{ $fieldName }}">
                            {{ $field->label }} 
                            @if($field->required) <span class="required-asterisk">*</span> @endif
                        </label>

                        @if($field->type === 'textarea')
                            <textarea name="{{ $fieldName }}" id="{{ $fieldName }}" class="form-control" placeholder="Nhập câu trả lời của bạn..." rows="3">{{ old($fieldName) }}</textarea>
                        
                        @elseif($field->type === 'select')
                            <select name="{{ $fieldName }}" id="{{ $fieldName }}" class="form-control">
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
                                <input type="file" name="{{ $fieldName }}" id="{{ $fieldName }}">
                                <i class="fa-solid fa-cloud-arrow-up file-icon"></i>
                                <div class="file-text">Kéo thả file vào đây hoặc <span>bấm để chọn file</span></div>
                                <div style="font-size: 12px; color: #94a3b8; margin-top: 8px;">(Hỗ trợ ảnh, PDF, Word - Tối đa 5MB)</div>
                            </div>
                        
                        @else
                            <input type="{{ $field->type === 'phone' ? 'tel' : ($field->type === 'email' ? 'email' : 'text') }}" 
                                   name="{{ $fieldName }}" 
                                   id="{{ $fieldName }}" 
                                   class="form-control"
                                   value="{{ old($fieldName) }}"
                                   placeholder="Nhập thông tin...">
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

</body>
</html>