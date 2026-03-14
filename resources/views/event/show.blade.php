<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $event->title }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { 
            font-family: 'Inter', 'Segoe UI', sans-serif; 
            background-color: #f0f2f5; 
            margin: 0; 
            padding: 0; 
            color: #1c1e21;
        }
        
        .event-container {
            max-width: 800px;
            margin: 30px auto;
            padding: 0 15px;
        }

        /* Ảnh Cover Sự kiện */
        .event-cover {
            width: 100%;
            height: 300px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 12px 12px 0 0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 48px;
            opacity: 0.9;
        }

        /* Khối Thông tin chính */
        .event-header {
            background: #fff;
            padding: 24px;
            border-radius: 0 0 12px 12px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }

        .event-date-badge {
            color: #e53e3e;
            font-weight: 700;
            font-size: 15px;
            text-transform: uppercase;
            margin-bottom: 8px;
            display: block;
        }

        .event-title {
            font-size: 28px;
            font-weight: 700;
            margin: 0 0 15px 0;
        }

        /* Nút Hành động */
        .action-bar {
            display: flex;
            gap: 12px;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #e4e6eb;
        }

        .btn {
            padding: 10px 24px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 15px;
            cursor: pointer;
            border: none;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: 0.2s;
        }

        .btn-primary { background: #1877f2; color: #fff; }
        .btn-primary:hover { background: #166fe5; }
        
        .btn-secondary { background: #e4e6eb; color: #050505; }
        .btn-secondary:hover { background: #d8dadf; }

        .btn-active { background: #e7f3ff; color: #1877f2; }
        .btn-active:hover { background: #dbeafe; }

        /* Khối Chi tiết Sự kiện */
        .event-details {
            background: #fff;
            padding: 24px;
            border-radius: 12px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.1);
        }

        .detail-item {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
            align-items: flex-start;
        }

        .detail-icon {
            width: 40px;
            height: 40px;
            background: #f0f2f5;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            color: #1c1e21;
        }

        .detail-text h4 { margin: 0 0 4px 0; font-size: 16px; font-weight: 600; }
        .detail-text p { margin: 0; color: #65676b; font-size: 15px; line-height: 1.5; white-space: pre-wrap;}

        .alert-success { background: #e6f4ea; color: #137333; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; font-weight: 500; }
    </style>
</head>
<body>

<div class="event-container">
    @if(session('success'))
        <div class="alert-success"><i class="fa-solid fa-circle-check"></i> {{ session('success') }}</div>
    @endif

    <div class="event-cover">
        <i class="fa-regular fa-calendar-check"></i>
    </div>

    <div class="event-header">
        <span class="event-date-badge">
            {{ \Carbon\Carbon::parse($event->start_time)->locale('vi')->translatedFormat('l, j F, Y') }}
        </span>
        <h1 class="event-title">{{ $event->title }}</h1>
        <div style="color: #65676b; font-size: 15px;">
            Sự kiện do <strong>{{ $event->page ? $event->page->name : 'Ban tổ chức' }}</strong> tổ chức
        </div>

        @php
            // HACK CHO VIỆC TEST: Lấy User thật hoặc User đầu tiên trong Database
            $testUser = auth()->user() ?? \App\Models\User::first();
            $isGoing = false;
            
            if($testUser) {
                // Kiểm tra xem Test User này đã đăng ký tham gia chưa
                $participant = $event->participants()->where('user_id', $testUser->id)->first();
                $isGoing = $participant && $participant->pivot->status === 'going';
            }
        @endphp

        <div class="action-bar">
            @if($testUser)
                @if($isGoing)
                    <div style="display: flex; gap: 10px; align-items: center;">
                        <form action="{{ route('events.join', $event->id) }}" method="POST">
                            @csrf
                            <input type="hidden" name="status" value="not_going">
                            <button type="submit" class="btn btn-active" title="Bấm để hủy đăng ký" onclick="return confirm('Bạn có chắc chắn muốn hủy đăng ký tham gia sự kiện này?')">
                                <i class="fa-solid fa-circle-check"></i> Đã đăng ký
                            </button>
                        </form>

                        @if($event->forms && $event->forms->count() > 0)
                            @php $proofForm = $event->forms->first(); @endphp
                            <a href="{{ route('forms.show', $proofForm->id) }}" class="btn" style="background: #673ab7; color: white;">
                                <i class="fa-solid fa-file-arrow-up"></i> Nộp minh chứng
                            </a>
                        @endif
                    </div>
                @else
                    <form action="{{ route('events.join', $event->id) }}" method="POST">
                        @csrf
                        <input type="hidden" name="status" value="going">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa-solid fa-pen-to-square"></i> Đăng ký
                        </button>
                    </form>
                @endif
            @else
                <div style="color: #d93025; font-size: 14px; font-weight: 500;">
                    <i class="fa-solid fa-triangle-exclamation"></i> Không tìm thấy User nào trong Database!
                </div>
            @endif
        </div>
    </div>

    <div class="event-details">
        <h3 style="margin-top: 0; font-size: 20px; margin-bottom: 20px;">Chi tiết</h3>
        
        <div class="detail-item">
            <div class="detail-icon"><i class="fa-regular fa-clock"></i></div>
            <div class="detail-text">
                <h4>Thời gian</h4>
                <p>{{ \Carbon\Carbon::parse($event->start_time)->format('H:i d/m/Y') }} - {{ \Carbon\Carbon::parse($event->end_time)->format('H:i d/m/Y') }}</p>
            </div>
        </div>

        <div class="detail-item">
            <div class="detail-icon"><i class="fa-solid fa-location-dot"></i></div>
            <div class="detail-text">
                <h4>Địa điểm</h4>
                <p>{{ $event->location ?? 'Đang cập nhật' }}</p>
            </div>
        </div>

        <div class="detail-item">
            <div class="detail-icon"><i class="fa-solid fa-circle-info"></i></div>
            <div class="detail-text">
                <h4>Giới thiệu</h4>
                <p>{{ $event->description ?? 'Chưa có thông tin mô tả cho sự kiện này.' }}</p>
            </div>
        </div>
    </div>
</div>

</body>
</html>