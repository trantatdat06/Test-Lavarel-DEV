<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản trị hệ thống - Phê duyệt Trang</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f0f2f5; margin: 0; padding: 20px; font-family: system-ui, -apple-system, sans-serif; }
        .admin-container { background: #fff; border-radius: 16px; padding: 25px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); max-width: 1200px; margin: auto; }
        .admin-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; border-bottom: 1px solid #f0f2f5; padding-bottom: 15px; }
        .admin-header h2 { font-size: 20px; font-weight: 700; color: #1c1e21; margin: 0; }
        .alert { padding: 15px; border-radius: 8px; margin-bottom: 20px; font-weight: 500; }
        .alert-success { background: #e7f3ff; color: #1877f2; border: 1px solid #1877f2; }
        .alert-error { background: #fce8e6; color: #dc3545; border: 1px solid #dc3545; }
        .req-table { width: 100%; border-collapse: collapse; }
        .req-table th { background: #f0f2f5; color: #65676b; font-size: 14px; padding: 12px 15px; text-align: left; }
        .req-table td { padding: 15px; border-bottom: 1px solid #f0f2f5; font-size: 14px; vertical-align: top; }
        .user-name { font-weight: 700; }
        .page-name { font-weight: 700; color: #1877f2; }
        .page-category { background: #e4e6eb; padding: 3px 8px; border-radius: 4px; font-size: 11px; font-weight: 700; }
        .btn { padding: 8px 15px; border: none; border-radius: 6px; font-weight: 600; cursor: pointer; width: 100%; margin-bottom: 5px; }
        .btn-approve { background: #23a559; color: #fff; }
        .btn-reject-trigger { background: #e4e6eb; color: #1c1e21; }
        .reject-form { display: none; margin-top: 10px; background: #fce8e6; padding: 10px; border-radius: 6px; }
        .reject-input { width: 100%; padding: 8px; border-radius: 4px; border: 1px solid #ccd0d5; margin-bottom: 5px; box-sizing: border-box; }
    </style>
</head>
<body>
    <div class="admin-container">
        <div class="admin-header">
            <h2><i class="fa-solid fa-shield-halved"></i> Quản lý phê duyệt Trang & CLB</h2>
            <span>Chờ duyệt: {{ count($pendingPages) }}</span>
        </div>

        @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
        @if(session('error')) <div class="alert alert-error">{{ session('error') }}</div> @endif

        <table class="req-table">
            <thead>
                <tr>
                    <th>Người gửi</th>
                    <th>Thông tin Trang</th>
                    <th>Mục đích</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pendingPages as $page)
                <tr>
                    <td><span class="user-name">{{ $page->full_name }}</span><br><small>{{ $page->email }}</small></td>
                    <td><span class="page-name">{{ $page->name }}</span><br><span class="page-category">{{ strtoupper($page->category) }}</span></td>
                    <td><div style="background: #f0f2f5; padding: 10px; border-radius: 6px;">{{ $page->description }}</div></td>
                    <td>
                        <form action="{{ route('admin.page.approve', $page->id) }}" method="POST">
                            @csrf
                            <button class="btn btn-approve">Duyệt</button>
                        </form>
                        <button class="btn btn-reject-trigger" onclick="document.getElementById('reject-{{$page->id}}').style.display='block'">Từ chối</button>
                        <div id="reject-{{$page->id}}" class="reject-form">
                            <form action="{{ route('admin.page.reject', $page->id) }}" method="POST">
                                @csrf
                                <input type="text" name="reject_reason" class="reject-input" placeholder="Lý do từ chối..." required>
                                <button class="btn" style="background: #dc3545; color: #fff;">Xác nhận</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" style="text-align: center; padding: 50px; color: #8e8e8e;">Không có yêu cầu nào.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</body>
</html>