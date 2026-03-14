<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý đơn - {{ $form->title }}</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f3f4f6; padding: 40px 20px; margin: 0; }
        .container { max-width: 1000px; margin: 0 auto; background: #ffffff; padding: 30px; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        
        .header-section { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #e5e7eb; padding-bottom: 15px; margin-bottom: 20px; }
        h1 { font-size: 24px; color: #111827; margin: 0; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #e5e7eb; padding: 12px 15px; text-align: left; font-size: 15px; }
        th { background-color: #f9fafb; color: #374151; font-weight: 600; }
        td { vertical-align: top; }
        
        .data-list { margin: 0; padding-left: 20px; color: #4b5563; }
        .data-list li { margin-bottom: 5px; }
        
        /* Màu sắc Trạng thái */
        .badge { padding: 6px 10px; border-radius: 20px; font-size: 13px; font-weight: 600; display: inline-block; text-align: center; width: 80px;}
        .bg-pending { background-color: #fef08a; color: #854d0e; }
        .bg-approved { background-color: #bbf7d0; color: #166534; }
        .bg-rejected { background-color: #fecaca; color: #991b1b; }
        
        /* Nút bấm hành động */
        .btn { padding: 8px 12px; border: none; border-radius: 6px; cursor: pointer; font-size: 14px; font-weight: 600; color: white; transition: 0.2s; margin-right: 5px; margin-bottom: 5px; text-decoration: none;}
        .btn-approve { background-color: #10b981; }
        .btn-approve:hover { background-color: #059669; }
        .btn-reject { background-color: #ef4444; }
        .btn-reject:hover { background-color: #dc2626; }
        
        /* Nút xuất file Excel mới */
        .btn-export { background-color: #16a34a; display: flex; align-items: center; gap: 6px; }
        .btn-export:hover { background-color: #15803d; }

        .alert-success { background-color: #def7ec; color: #03543f; padding: 15px; border-radius: 6px; margin-bottom: 20px; border: 1px solid #bcdecb; font-weight: 500; }
        .empty-message { text-align: center; padding: 30px; color: #6b7280; font-style: italic; }
    </style>
</head>
<body>

<div class="container">
    <div class="header-section">
        <h1>Quản lý đơn: {{ $form->title }}</h1>
        <a href="{{ route('forms.export', $form->id) }}" class="btn btn-export">
            📥 Xuất Excel (CSV)
        </a>
    </div>

    @if(session('success'))
        <div class="alert-success">✅ {{ session('success') }}</div>
    @endif

    <table>
        <thead>
            <tr>
                <th width="5%">ID</th>
                <th width="35%">Người nộp (Auto-fill)</th>
                <th width="30%">Câu trả lời tùy chỉnh</th>
                <th width="15%">Trạng thái</th>
                <th width="15%">Hành động</th>
            </tr>
        </thead>
        <tbody>
            @forelse($submissions as $sub)
                <tr>
                    <td>#{{ $sub->id }}</td>
                    
                    <td>
                        @if($sub->user)
                            <strong>{{ $sub->user->display_name }}</strong><br>
                            <span style="font-size: 13px; color: #6b7280;">Mã SV: {{ $sub->user->student_code }}</span><br>
                            <span style="font-size: 13px; color: #6b7280;">Email: {{ $sub->user->email }}</span>
                        @else
                            <span style="color: #9ca3af; font-style: italic;">Người dùng Khách</span>
                        @endif
                    </td>

                    <td>
                        <ul class="data-list">
                            @foreach($sub->data as $key => $value)
                                <li>
                                    @if(is_string($value) && str_starts_with($value, 'form_submissions/'))
                                        <a href="{{ asset('storage/' . $value) }}" target="_blank" style="color: #2563eb;">Xem tệp đính kèm</a>
                                    @else
                                        {{ $value }}
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </td>

                    <td>
                        @if($sub->status == 'pending')
                            <span class="badge bg-pending">Chờ duyệt</span><br>
                            <span style="font-size: 12px; color: #9ca3af; margin-top: 4px; display: block;">{{ \Carbon\Carbon::parse($sub->submitted_at)->format('H:i d/m/Y') }}</span>
                        @elseif($sub->status == 'approved')
                            <span class="badge bg-approved">Đã duyệt</span>
                        @elseif($sub->status == 'rejected')
                            <span class="badge bg-rejected">Từ chối</span>
                        @else
                            <span class="badge" style="background: #e5e7eb; color: #374151;">Đã hủy</span>
                        @endif
                    </td>

                    <td>
                        <form action="{{ route('submissions.update', $sub->id) }}" method="POST" style="display:inline-block;">
                            @csrf 
                            @method('PUT') <input type="hidden" name="status" value="approved">
                            <button type="submit" class="btn btn-approve" onclick="return confirm('Bạn chắc chắn muốn DUYỆT đơn này?')">Duyệt</button>
                        </form>

                        <form action="{{ route('submissions.update', $sub->id) }}" method="POST" style="display:inline-block;">
                            @csrf 
                            @method('PUT')
                            <input type="hidden" name="status" value="rejected">
                            <button type="submit" class="btn btn-reject" onclick="return confirm('Bạn chắc chắn muốn TỪ CHỐI đơn này?')">Từ chối</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="empty-message">Hiện chưa có ai nộp biểu mẫu này.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

</body>
</html>