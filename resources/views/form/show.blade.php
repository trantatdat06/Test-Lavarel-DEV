<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $form->title }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { 
            font-family: 'Google Sans', 'Roboto', 'Helvetica Neue', sans-serif; 
            background-color: #f0ebf8; 
            min-height: 100vh; 
            display: flex; 
            flex-direction: column;
            align-items: center; 
            margin: 0; 
            padding: 20px 15px 60px 15px; 
            box-sizing: border-box;
            color: #202124;
        }
        
        .form-container { width: 100%; max-width: 640px; }

        .form-header-card {
            background: #ffffff; border-radius: 8px; border-top: 10px solid #673ab7; 
            padding: 24px; box-shadow: 0 1px 2px rgba(0,0,0,0.1); margin-bottom: 12px;
        }
        
        .form-title { font-size: 32px; font-weight: 400; color: #202124; margin: 0 0 12px 0; line-height: 1.2;}
        .form-desc { color: #202124; font-size: 14px; margin-bottom: 20px; line-height: 1.5; white-space: pre-wrap;}
        
        .account-info-section { border-top: 1px solid #dadce0; padding-top: 15px; font-size: 13px; color: #5f6368; }
        .account-email { font-weight: 600; color: #3c4043; }
        .text-link { color: #1a73e8; text-decoration: none; font-weight: 500; cursor: pointer;}
        .text-link:hover { text-decoration: underline; }

        .question-card {
            background: #ffffff; border-radius: 8px; padding: 24px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.1); margin-bottom: 12px;
            transition: 0.2s; border-left: 3px solid transparent;
        }
        .question-card:focus-within { border-left: 3px solid #4285f4; }
        
        label { display: block; font-weight: 400; margin-bottom: 16px; color: #202124; font-size: 16px; }
        .required-mark { color: #d93025; margin-left: 4px; }
        
        input[type="text"], input[type="email"], input[type="tel"], textarea { 
            width: 100%; padding: 8px 0; border: none; border-bottom: 1px solid #dadce0; 
            box-sizing: border-box; font-family: inherit; font-size: 14px; 
            background-color: transparent; color: #202124; transition: 0.3s;
        }
        input:focus, textarea:focus { outline: none; border-bottom: 2px solid #673ab7; }
        
        select { width: 50%; padding: 10px; border: 1px solid #dadce0; border-radius: 4px; font-size: 14px; color: #202124; outline: none; background: #fff; }
        select:focus { border: 2px solid #673ab7; padding: 9px;}
        
        .btn-submit { background: #673ab7; color: white; padding: 10px 24px; border: none; border-radius: 4px; cursor: pointer; font-size: 14px; font-weight: 500; transition: 0.2s; }
        .btn-submit:hover { background: #5e35b1; box-shadow: 0 1px 3px rgba(0,0,0,0.2);}
        
        .btn-clear { background: transparent; border: none; color: #673ab7; font-size: 14px; font-weight: 500; cursor: pointer; padding: 10px 16px; border-radius: 4px; transition: 0.2s; }
        .btn-clear:hover { background: #f3e8fd; }

        .text-danger { color: #d93025; font-size: 12px; margin-top: 8px; display: flex; align-items: center; gap: 4px;}
        .alert-success { background-color: #e6f4ea; color: #137333; padding: 16px; border-radius: 8px; margin-bottom: 15px; border: 1px solid #ceead6; font-weight: 500; }
        .alert-warning { background-color: #fef7e0; color: #b06000; padding: 16px; border-radius: 8px; margin-bottom: 15px; border: 1px solid #fdd663; font-size: 14px;}
        
        /* CSS CHO KHỐI AUTO-FILL XANH LÁ */
        .autofill-card { background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; padding: 24px; margin-bottom: 12px; box-shadow: 0 1px 2px rgba(0,0,0,0.05); }
        .autofill-title { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; font-weight: 600; color: #166534; font-size: 15px;}
        .autofill-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
        .autofill-item { display: flex; flex-direction: column; background: #fff; padding: 10px 15px; border-radius: 6px; border: 1px solid #dcfce7;}
        .autofill-label { font-size: 11px; color: #15803d; margin-bottom: 4px; text-transform: uppercase; font-weight: 700; letter-spacing: 0.5px;}
        .autofill-value { margin: 0; font-size: 15px; color: #14532d; font-weight: 500; }
    </style>
</head>
<body>

@php
    // HACK CHO VIỆC TEST: Tự động lấy User đầu tiên trong DB nếu chưa đăng nhập
    $testUser = auth()->user() ?? \App\Models\User::first();
@endphp

<div class="form-container">
    @if(session('success'))
        <div class="alert-success"><i class="fa-solid fa-circle-check"></i> {{ session('success') }}</div>
    @endif

    <form action="{{ route('forms.submit', $form->id) }}" method="POST" enctype="multipart/form-data">
        @csrf 
        
        <div class="form-header-card">
            <h1 class="form-title">{{ $form->title }}</h1>
            @if($form->description)
                <p class="form-desc">{{ $form->description }}</p>
            @endif
            
            <div class="account-info-section">
                @if($testUser)
                    <div>
                        <span class="account-email">{{ $testUser->display_name }} ({{ $testUser->email }})</span>
                        <a href="#" class="text-link" style="margin-left: 5px;">Chuyển đổi tài khoản</a>
                    </div>
                    <div style="margin-top: 8px;">
                        <i class="fa-solid fa-cloud-arrow-up" style="color: #5f6368; margin-right: 5px;"></i> Đã lưu vào bản nháp
                    </div>
                    <div style="margin-top: 15px; color: #d93025; font-size: 13px;">
                        * Biểu thị câu hỏi bắt buộc
                    </div>
                @else
                    <span style="color: #d93025; font-weight: 500;">⚠️ Bạn đang ở chế độ Khách. Vui lòng chạy lệnh db:seed để tạo dữ liệu test!</span>
                @endif
            </div>
        </div>

        @if($testUser)
            <div class="autofill-card">
                <div class="autofill-title">
                    <span><i class="fa-solid fa-shield-halved"></i> Thông tin Sinh viên (Tự động đính kèm)</span>
                    <span style="color: #10b981; font-weight: 400; font-size: 13px;"><i class="fa-solid fa-check"></i> Đã xác thực</span>
                </div>
                <div class="autofill-grid">
                    <div class="autofill-item">
                        <span class="autofill-label">Họ và tên hiển thị</span>
                        <p class="autofill-value">{{ $testUser->display_name }}</p>
                    </div>
                    <div class="autofill-item">
                        <span class="autofill-label">Mã Sinh viên</span>
                        <p class="autofill-value">{{ $testUser->student_code ?? 'Chưa cập nhật' }}</p>
                    </div>
                    <div class="autofill-item" style="grid-column: span 2;">
                        <span class="autofill-label">Email trường</span>
                        <p class="autofill-value">{{ $testUser->email }}</p>
                    </div>
                </div>
                <p style="margin: 15px 0 0 0; font-size: 12px; color: #166534; font-style: italic;">* Hệ thống tự động trích xuất thông tin từ tài khoản để đảm bảo tính minh bạch và cộng điểm rèn luyện.</p>
            </div>
        @endif

        @foreach($form->fields as $field)
            @php $fieldName = 'field_' . $field->id; @endphp
            
            <div class="question-card">
                <label for="{{ $fieldName }}">
                    {{ $field->label }} 
                    @if($field->required) <span class="required-mark">*</span> @endif
                </label>

                @if($field->type === 'textarea')
                    <textarea name="{{ $fieldName }}" id="{{ $fieldName }}" rows="1" placeholder="Câu trả lời của bạn" oninput="this.style.height = ''; this.style.height = this.scrollHeight + 'px'">{{ old($fieldName) }}</textarea>
                
                @elseif($field->type === 'select')
                    <select name="{{ $fieldName }}" id="{{ $fieldName }}">
                        <option value="">Chọn</option>
                        @if($field->options)
                            @foreach($field->options as $option)
                                <option value="{{ $option }}" {{ old($fieldName) == $option ? 'selected' : '' }}>
                                    {{ $option }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                
                @elseif($field->type === 'file')
                    <input type="file" name="{{ $fieldName }}" id="{{ $fieldName }}" style="background: transparent; border: none; padding: 10px 0;">
                
                @else
                    <input type="{{ $field->type === 'phone' ? 'tel' : ($field->type === 'email' ? 'email' : 'text') }}" 
                           name="{{ $fieldName }}" 
                           id="{{ $fieldName }}" 
                           value="{{ old($fieldName) }}"
                           placeholder="Câu trả lời của bạn">
                @endif

                @error($fieldName)
                    <span class="text-danger"><i class="fa-solid fa-circle-exclamation"></i> Đây là một câu hỏi bắt buộc</span>
                @enderror
            </div>
        @endforeach

        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 15px;">
            <button type="submit" class="btn-submit">Gửi</button>
            <button type="reset" class="btn-clear">Xóa hết câu trả lời</button>
        </div>
        
    </form>
</div>

</body>
</html>