<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tạo Biểu Mẫu Đăng Ký</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Inter', 'Segoe UI', sans-serif;
            background-color: #f3f4f6;
            margin: 0;
            padding: 0;
            color: #1f2937;
        }

        /* Thanh Top Navigation */
        .top-navbar {
            background: #ffffff;
            padding: 12px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #e5e7eb;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .nav-left { display: flex; align-items: center; gap: 20px; }
        .btn-back { color: #6b7280; text-decoration: none; font-weight: 500; display: flex; align-items: center; gap: 8px; font-size: 15px; }
        .btn-back:hover { color: #111827; }
        .page-title { font-size: 18px; font-weight: 700; margin: 0; color: #111827; border-left: 2px solid #e5e7eb; padding-left: 20px; }
        
        .nav-right { display: flex; gap: 12px; }
        .btn-outline { background: #fff; border: 1px solid #d1d5db; padding: 8px 16px; border-radius: 6px; font-weight: 600; color: #374151; cursor: pointer; transition: 0.2s; }
        .btn-outline:hover { background: #f9fafb; }
        .btn-primary { background: #4f46e5; border: none; padding: 8px 20px; border-radius: 6px; font-weight: 600; color: white; cursor: pointer; transition: 0.2s; box-shadow: 0 1px 2px rgba(0,0,0,0.05); }
        .btn-primary:hover { background: #4338ca; }

        /* Container 2 cột */
        .main-container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 20px;
            display: grid;
            grid-template-columns: 350px 1fr;
            gap: 25px;
            align-items: start;
        }

        /* Thẻ Card dùng chung */
        .card {
            background: #ffffff;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            border: 1px solid #f3f4f6;
        }
        .card-title { font-size: 16px; font-weight: 700; color: #111827; margin-top: 0; margin-bottom: 20px; padding-bottom: 12px; border-bottom: 1px solid #e5e7eb; display: flex; justify-content: space-between; align-items: center;}

        /* CSS cho các Input */
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; font-size: 14px; font-weight: 600; color: #374151; margin-bottom: 8px; }
        .form-control {
            width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; font-family: inherit; color: #111827; box-sizing: border-box; transition: 0.2s; background: #f9fafb;
        }
        .form-control:focus { outline: none; border-color: #4f46e5; background: #fff; box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1); }
        
        textarea.form-control { resize: vertical; min-height: 100px; }

        /* Khung Upload Ảnh Cover */
        .upload-zone { border: 2px dashed #d1d5db; border-radius: 8px; padding: 30px 20px; text-align: center; cursor: pointer; background: #f9fafb; transition: 0.2s; }
        .upload-zone:hover { border-color: #4f46e5; background: #eef2ff; }
        .upload-zone i { font-size: 30px; color: #9ca3af; margin-bottom: 10px; }
        .upload-zone p { margin: 0; font-size: 13px; color: #6b7280; font-weight: 500;}

        /* Nút thêm câu hỏi */
        .btn-add-q { background: #eef2ff; color: #4f46e5; border: 1px solid #c7d2fe; padding: 6px 12px; border-radius: 6px; font-size: 13px; font-weight: 600; cursor: pointer; transition: 0.2s; }
        .btn-add-q:hover { background: #e0e7ff; }

        /* Khối Câu Hỏi Động */
        .q-block { background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; padding: 16px; margin-bottom: 15px; position: relative; transition: 0.2s; border-left: 4px solid #9ca3af; }
        .q-block:focus-within { border-left-color: #4f46e5; background: #fff; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        
        .q-row { display: flex; gap: 15px; margin-bottom: 15px; }
        .q-input-title { flex: 2; border: none; border-bottom: 1px solid #d1d5db; background: transparent; padding: 8px 0; font-size: 15px; font-weight: 500; outline: none; transition: 0.2s; }
        .q-input-title:focus { border-bottom-color: #4f46e5; }
        .q-select-type { flex: 1; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; background: #fff; outline: none; font-size: 13px; cursor: pointer; }

        .q-footer { display: flex; justify-content: flex-end; align-items: center; gap: 15px; border-top: 1px solid #e5e7eb; padding-top: 12px; }
        .btn-delete-q { background: transparent; border: none; color: #9ca3af; font-size: 16px; cursor: pointer; transition: 0.2s; }
        .btn-delete-q:hover { color: #ef4444; }
        .toggle-req { display: flex; align-items: center; gap: 6px; font-size: 13px; color: #4b5563; font-weight: 500; cursor: pointer; }
    </style>
</head>
<body>

<form action="{{ route('forms.store') }}" method="POST" id="form-builder" enctype="multipart/form-data">
    @csrf

    <div class="top-navbar">
        <div class="nav-left">
            <a href="javascript:history.back()" class="btn-back"><i class="fa-solid fa-arrow-left"></i> Quay lại</a>
            <h1 class="page-title">Tạo Biểu Mẫu Đăng Ký</h1>
        </div>
        <div class="nav-right">
            <button type="button" class="btn-outline"><i class="fa-regular fa-eye"></i> Bản xem trước</button>
            <button type="submit" class="btn-primary"><i class="fa-solid fa-floppy-disk"></i> Lưu và Tiếp tục</button>
        </div>
    </div>

    <div class="main-container">
        
        <div class="left-col">
            <div class="card">
                <h3 class="card-title">Cấu hình chung</h3>
                
                <div class="form-group">
                    <label>Tên sự kiện / Tiêu đề Form <span style="color:red">*</span></label>
                    <input type="text" name="title" class="form-control" placeholder="Nhập tên sự kiện..." required>
                </div>

                <div class="form-group">
                    <label>Nội dung sự kiện</label>
                    <textarea name="description" class="form-control" placeholder="Mô tả chi tiết về sự kiện này..."></textarea>
                </div>

                <div class="form-group">
                    <label>Thời hạn đóng form (Tùy chọn)</label>
                    <input type="datetime-local" name="deadline" class="form-control">
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold"><i class="fa-solid fa-tags"></i> Chủ đề biểu mẫu (Tags)</label>
                    <p style="font-size: 12px; color: #65676b; margin-bottom: 8px;">Nhấn vào các thẻ bên dưới để gắn tag cho Form</p>
                    
                    <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                        @if(isset($availableTags) && count($availableTags) > 0)
                            @foreach($availableTags as $tag)
                                <input type="checkbox" name="tags[]" id="form_tag_{{ $tag->id }}" value="{{ $tag->name }}" class="form-tag-checkbox" style="display: none;">
                                
                                <label for="form_tag_{{ $tag->id }}" class="form-tag-label" style="padding: 6px 14px; background-color: #f0f2f5; color: #65676b; border-radius: 20px; font-size: 13px; cursor: pointer; border: 1px solid transparent; transition: all 0.2s ease; user-select: none;">
                                    #{{ $tag->name }}
                                </label>
                            @endforeach
                        @else
                            <p class="text-muted small">Chưa có chủ đề nào trong hệ thống</p>
                        @endif
                    </div>
                </div>

                <style>
                    .form-tag-checkbox:checked + .form-tag-label {
                        background-color: #e7f3ff !important;
                        color: #1877f2 !important;
                        border-color: #1877f2 !important;
                        font-weight: 500;
                    }
                </style>

            </div>
        </div>

        <div class="right-col">
            <div class="card">
                <h3 class="card-title">
                    Cấu trúc Form
                    <button type="button" class="btn-add-q" onclick="addQuestion()"><i class="fa-solid fa-plus"></i> Thêm câu hỏi</button>
                </h3>
                
                <div id="questions-area">
                    </div>

                <div style="text-align: center; margin-top: 20px; padding: 20px; border: 2px dashed #e5e7eb; border-radius: 8px; color: #6b7280; font-size: 14px;">
                    Nhấn <strong>"+ Thêm câu hỏi"</strong> ở góc trên để bổ sung trường thông tin
                </div>
            </div>
        </div>

    </div>
</form>

<script>
    let questionIndex = 0;

    function addQuestion() {
        const container = document.getElementById('questions-area');
        
        const qHTML = `
            <div class="q-block" id="q-block-${questionIndex}">
                <div class="q-row">
                    <input type="text" name="fields[${questionIndex}][label]" class="q-input-title" placeholder="Nhập tiêu đề câu hỏi..." required>
                    
                    <select name="fields[${questionIndex}][type]" class="q-select-type">
                        <option value="text">Văn bản ngắn</option>
                        <option value="textarea">Đoạn văn dài</option>
                        <option value="email">Địa chỉ Email</option>
                        <option value="phone">Số điện thoại</option>
                        <option value="file">Tải tệp đính kèm</option>
                    </select>
                </div>

                <div class="q-footer">
                    <label class="toggle-req">
                        <input type="hidden" name="fields[${questionIndex}][required]" value="0">
                        <input type="checkbox" name="fields[${questionIndex}][required]" value="1" checked>
                        Bắt buộc trả lời
                    </label>
                    <div style="width: 1px; height: 16px; background: #d1d5db;"></div>
                    <button type="button" class="btn-delete-q" onclick="removeQuestion(${questionIndex})" title="Xóa câu hỏi">
                        <i class="fa-solid fa-trash-can"></i>
                    </button>
                </div>
            </div>
        `;
        
        container.insertAdjacentHTML('beforeend', qHTML);
        document.querySelector(`#q-block-${questionIndex} .q-input-title`).focus();
        questionIndex++;
    }

    function removeQuestion(index) {
        const block = document.getElementById(`q-block-${index}`);
        if(block) block.remove();
    }

    // Tự động tạo 1 câu hỏi mẫu khi load trang
    window.onload = function() {
        addQuestion();
    };
</script>

</body>
</html>