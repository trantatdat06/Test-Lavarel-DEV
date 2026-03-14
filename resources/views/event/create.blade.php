<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tạo Sự Kiện Mới</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Inter', 'Segoe UI', sans-serif;
            background-color: #f3f4f6;
            margin: 0;
            padding: 40px 20px;
            color: #1f2937;
            display: flex;
            justify-content: center;
        }

        .create-container {
            width: 100%;
            max-width: 700px;
        }

        .header-title {
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .header-title h1 { font-size: 24px; margin: 0; color: #111827; }
        .btn-back { color: #6b7280; text-decoration: none; font-size: 18px; transition: 0.2s; }
        .btn-back:hover { color: #111827; }

        .form-card {
            background: #ffffff;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            border: 1px solid #e5e7eb;
        }

        .form-group { margin-bottom: 22px; }
        .form-group label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 8px;
        }
        .required { color: #ef4444; }

        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 15px;
            font-family: inherit;
            color: #111827;
            background-color: #f9fafb;
            box-sizing: border-box;
            transition: all 0.2s ease;
        }
        .form-control:focus {
            outline: none;
            border-color: #3b82f6;
            background-color: #ffffff;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .grid-2-cols {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        textarea.form-control {
            resize: vertical;
            min-height: 120px;
        }

        .btn-submit {
            background: #1877f2;
            color: white;
            border: none;
            padding: 14px 24px;
            font-size: 16px;
            font-weight: 600;
            border-radius: 8px;
            cursor: pointer;
            width: 100%;
            transition: 0.2s;
            margin-top: 10px;
        }
        .btn-submit:hover { background: #166fe5; }

        .text-danger { color: #ef4444; font-size: 13px; margin-top: 6px; display: block; }
        .alert-error { background: #fee2e2; color: #991b1b; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #fecaca; font-size: 14px;}
    </style>
</head>
<body>

<div class="create-container">
    <div class="header-title">
        <a href="javascript:history.back()" class="btn-back"><i class="fa-solid fa-arrow-left"></i></a>
        <h1>Tạo sự kiện mới</h1>
    </div>

    @if ($errors->any())
        <div class="alert-error">
            <i class="fa-solid fa-triangle-exclamation"></i> Vui lòng kiểm tra lại các thông tin bên dưới.
        </div>
    @endif

    <div class="form-card">
        <form action="{{ route('events.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label>Tên sự kiện <span class="required">*</span></label>
                <input type="text" name="title" class="form-control" value="{{ old('title') }}" placeholder="VD: Lễ khai giảng năm học 2026..." required>
                @error('title') <span class="text-danger">{{ $message }}</span> @enderror
            </div>

            <div class="grid-2-cols">
                <div class="form-group">
                    <label>Thời gian bắt đầu <span class="required">*</span></label>
                    <input type="datetime-local" name="start_time" class="form-control" value="{{ old('start_time') }}" required>
                    @error('start_time') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label>Thời gian kết thúc <span class="required">*</span></label>
                    <input type="datetime-local" name="end_time" class="form-control" value="{{ old('end_time') }}" required>
                    @error('end_time') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="form-group">
                <label>Địa điểm tổ chức</label>
                <input type="text" name="location" class="form-control" value="{{ old('location') }}" placeholder="VD: Hội trường lớn D1...">
                @error('location') <span class="text-danger">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label>Giới thiệu chi tiết</label>
                <textarea name="description" class="form-control" placeholder="Mô tả các hoạt động, thành phần tham dự, lưu ý cho sinh viên...">{{ old('description') }}</textarea>
                @error('description') <span class="text-danger">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; font-size: 15px; color: #111827;">
                    <input type="checkbox" name="has_training_point" id="toggle_points" style="width: 18px; height: 18px;" onchange="togglePointConfig()">
                    <span><i class="fa-solid fa-star" style="color: #eab308;"></i> Sự kiện này có cấp Điểm rèn luyện</span>
                </label>

                <div id="point_config_area" style="display: none; margin-top: 15px;">
                    <div class="grid-2-cols">
                        <div class="form-group" style="margin-bottom: 0;">
                            <label>Số điểm cấp</label>
                            <input type="number" name="point_amount" class="form-control" placeholder="VD: 5" min="1">
                        </div>
                        <div class="form-group" style="margin-bottom: 0;">
                            <label>Tiêu chí rèn luyện</label>
                            <select name="point_category" class="form-control">
                                <option value="Học thuật & NCKH">Tiêu chí 1: Học thuật & NCKH</option>
                                <option value="Chấp hành Nội quy">Tiêu chí 2: Chấp hành Nội quy</option>
                                <option value="Tình nguyện & Xã hội">Tiêu chí 3: Tình nguyện & Xã hội</option>
                                <option value="Văn hóa & Thể thao">Tiêu chí 3: Văn hóa & Thể thao</option>
                                <option value="Kỹ năng mềm">Tiêu chí 4: Kỹ năng mềm</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <script>
                function togglePointConfig() {
                    const isChecked = document.getElementById('toggle_points').checked;
                    document.getElementById('point_config_area').style.display = isChecked ? 'block' : 'none';
                }
            </script>

            <div style="background: #eef2ff; border: 1px solid #c7d2fe; padding: 20px; border-radius: 8px; margin-bottom: 22px;">
                <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; font-size: 15px; font-weight: 600; color: #3730a3;">
                    <input type="checkbox" name="auto_create_form" checked style="width: 18px; height: 18px;">
                    <i class="fa-solid fa-wand-magic-sparkles"></i> Tự động tạo Form nộp minh chứng
                </label>
                <div style="font-size: 13px; color: #4f46e5; margin-top: 8px; margin-left: 28px;">
                    Hệ thống sẽ tự động sinh ra một biểu mẫu yêu cầu sinh viên tải ảnh minh chứng đính kèm với sự kiện này.
                </div>
            </div>

            <button type="submit" class="btn-submit"><i class="fa-solid fa-calendar-plus"></i> Tạo sự kiện</button>
        </form>
    </div>
</div>

</body>
</html>