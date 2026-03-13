<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $form->title }}</title>
    <style>
        /* Đặt nền gradient và căn giữa form ra giữa màn hình */
        body { 
            font-family: 'Inter', 'Segoe UI', sans-serif; 
            background: linear-gradient(135deg, #e0e7ff 0%, #ede9fe 100%); 
            min-height: 100vh; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            margin: 0; 
            padding: 20px; 
            box-sizing: border-box;
        }
        
        /* Bo góc mềm mại và đổ bóng sâu hơn cho thẻ Form */
        .form-container { 
            width: 100%; 
            max-width: 550px; 
            background: #ffffff; 
            padding: 40px; 
            border-radius: 16px; 
            box-shadow: 0 10px 25px rgba(0,0,0,0.08); 
        }
        
        .form-title { font-size: 26px; font-weight: 800; color: #111827; margin-top: 0; margin-bottom: 8px; text-align: center; }
        .form-desc { color: #6b7280; font-size: 15px; margin-bottom: 30px; line-height: 1.6; text-align: center; }
        
        .form-group { margin-bottom: 22px; }
        label { display: block; font-weight: 600; margin-bottom: 8px; color: #374151; font-size: 14px; }
        .required-mark { color: #ef4444; margin-left: 4px; font-weight: bold; }
        
        /* Làm đẹp các ô nhập liệu */
        input[type="text"], input[type="email"], input[type="tel"], textarea, select, input[type="file"] { 
            width: 100%; 
            padding: 14px; 
            border: 1px solid #d1d5db; 
            border-radius: 8px; 
            box-sizing: border-box; 
            font-family: inherit; 
            font-size: 15px; 
            background-color: #f9fafb;
            transition: all 0.2s ease;
        }
        input:focus, textarea:focus, select:focus { 
            outline: none; 
            border-color: #6366f1; 
            background-color: #ffffff;
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.15); 
        }
        
        /* Nút bấm nổi bật */
        .btn-submit { 
            background: linear-gradient(to right, #6366f1, #4f46e5); 
            color: white; 
            padding: 14px 20px; 
            border: none; 
            border-radius: 8px; 
            cursor: pointer; 
            font-size: 16px; 
            font-weight: 600; 
            width: 100%; 
            margin-top: 10px;
            transition: transform 0.1s, box-shadow 0.2s; 
        }
        .btn-submit:hover { box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3); transform: translateY(-1px); }
        .btn-submit:active { transform: translateY(1px); }
        
        .text-danger { color: #ef4444; font-size: 13px; margin-top: 6px; display: block; font-weight: 500;}
        .alert-success { background-color: #ecfdf5; color: #065f46; padding: 16px; border-radius: 8px; margin-bottom: 25px; border: 1px solid #a7f3d0; font-weight: 500; display: flex; align-items: center; gap: 8px;}
    </style>
</head>
<body>

<div class="form-container">
    <h1 class="form-title">{{ $form->title }}</h1>
    @if($form->description)
        <p class="form-desc">{{ $form->description }}</p>
    @endif

    @if(session('success'))
        <div class="alert-success">
            ✅ {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('forms.submit', $form->id) }}" method="POST" enctype="multipart/form-data">
        @csrf @foreach($form->fields as $field)
            @php $fieldName = 'field_' . $field->id; @endphp
            
            <div class="form-group">
                <label for="{{ $fieldName }}">
                    {{ $field->label }} 
                    @if($field->required) <span class="required-mark">*</span> @endif
                </label>

                @if($field->type === 'textarea')
                    <textarea name="{{ $fieldName }}" id="{{ $fieldName }}" rows="4">{{ old($fieldName) }}</textarea>
                
                @elseif($field->type === 'select')
                    <select name="{{ $fieldName }}" id="{{ $fieldName }}">
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
                    <input type="file" name="{{ $fieldName }}" id="{{ $fieldName }}">
                
                @else
                    <input type="{{ $field->type === 'phone' ? 'tel' : ($field->type === 'email' ? 'email' : 'text') }}" 
                           name="{{ $fieldName }}" 
                           id="{{ $fieldName }}" 
                           value="{{ old($fieldName) }}">
                @endif

                @error($fieldName)
                    <span class="text-danger">⚠️ {{ $message }}</span>
                @enderror
            </div>
        @endforeach

        <button type="submit" class="btn-submit">Gửi thông tin</button>
    </form>
</div>

</body>
</html>