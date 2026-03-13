<style>
    .data-container {
        display: flex;
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        margin-top: 20px;
        min-height: 500px;
        overflow: hidden;
        font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif;
    }

    /* Cột Sidebar bên trái */
    .data-sidebar {
        width: 240px;
        background: #f8f9fa;
        border-right: 1px solid #e4e6eb;
        padding: 20px 0;
        flex-shrink: 0;
    }
    .data-sidebar-title {
        font-size: 16px;
        font-weight: 700;
        color: #65676b;
        text-transform: uppercase;
        padding: 0 20px 15px;
        border-bottom: 1px solid #e4e6eb;
        margin-bottom: 10px;
        letter-spacing: 0.5px;
    }
    .data-menu-item {
        padding: 12px 20px;
        font-size: 15px;
        font-weight: 600;
        color: #4b4b4b;
        cursor: pointer;
        transition: 0.2s;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .data-menu-item:hover { background: #eef2ff; color: #1877f2; }
    .data-menu-item.active { background: #e7f3ff; color: #1877f2; border-right: 3px solid #1877f2; }

    /* Nội dung chính bên phải */
    .data-main {
        flex: 1;
        padding: 25px;
        overflow-x: auto;
    }
    
    .data-header-controls {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
        flex-wrap: wrap;
        gap: 15px;
    }

    .data-filters { display: flex; gap: 10px; align-items: center; }
    .filter-label { font-weight: 700; color: #1c1e21; font-size: 14px; margin-right: 5px; }
    .filter-select {
        padding: 8px 12px;
        border-radius: 8px;
        border: 1px solid #ccd0d5;
        outline: none;
        font-weight: 600;
        color: #1c1e21;
        cursor: pointer;
        background: #fff;
    }

    .btn-calc-score {
        background: #23a559;
        color: #fff;
        border: none;
        padding: 10px 20px;
        border-radius: 8px;
        font-weight: 700;
        cursor: pointer;
        transition: 0.2s;
        display: flex;
        align-items: center;
        gap: 8px;
        box-shadow: 0 2px 8px rgba(35, 165, 89, 0.2);
    }
    .btn-calc-score:hover { background: #1e8e4e; transform: translateY(-1px); }

    /* Bảng dữ liệu */
    .data-table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
    }
    .data-table th {
        background: #f0f2f5;
        color: #65676b;
        font-weight: 700;
        font-size: 13px;
        text-transform: uppercase;
        padding: 12px 15px;
        border-bottom: 2px solid #e4e6eb;
    }
    .data-table td {
        padding: 15px;
        border-bottom: 1px solid #f0f2f5;
        vertical-align: middle;
        font-size: 14px;
        color: #1c1e21;
    }
    .data-table tr:hover td { background: #fafafa; }

    /* Các nhãn trạng thái (Badge) */
    .badge { padding: 6px 10px; border-radius: 6px; font-size: 12px; font-weight: 700; display: inline-flex; align-items: center; gap: 5px; }
    .badge-approved { background: #e6f4ea; color: #1e8e3e; }
    .badge-pending { background: #fef7e0; color: #b06000; }
    .badge-rejected { background: #fce8e6; color: #d93025; }
    .badge-none { background: #f0f2f5; color: #65676b; }
    
    .status-dot { width: 8px; height: 8px; border-radius: 50%; display: inline-block; margin-right: 5px; }
    .dot-ongoing { background: #23a559; box-shadow: 0 0 0 2px #e6f4ea; }
    .dot-ended { background: #dc3545; }

    .btn-form {
        background: #e7f3ff;
        color: #1877f2;
        border: none;
        padding: 6px 12px;
        border-radius: 6px;
        font-weight: 600;
        cursor: pointer;
        font-size: 13px;
    }
    .btn-form:hover { background: #dbeaff; }

    @media (max-width: 800px) {
        .data-container { flex-direction: column; }
        .data-sidebar { width: 100%; display: flex; border-right: none; border-bottom: 1px solid #e4e6eb; padding: 10px; overflow-x: auto; }
        .data-sidebar-title { display: none; }
        .data-menu-item { white-space: nowrap; border-radius: 8px; }
        .data-menu-item.active { border-right: none; background: #e7f3ff; }
    }
</style>

@php
    // Kiểm tra xem cột proof_status đã được thêm vào database chưa để tránh lỗi trắng trang
    $hasProofColumns = \Illuminate\Support\Facades\Schema::hasColumn('event_participants', 'proof_status');

    if ($hasProofColumns) {
        $events = \Illuminate\Support\Facades\DB::table('event_participants')
            ->join('events', 'event_participants.event_id', '=', 'events.id')
            ->where('event_participants.user_id', $user->id)
            ->where('event_participants.status', 'going') // Chỉ lấy sự kiện tham gia
            ->select(
                'events.id',
                'events.title',
                'events.start_time',
                'events.end_time',
                'event_participants.proof_status'
            )
            ->orderBy('events.start_time', 'desc')
            ->get();
    } else {
        $events = collect(); // Trả về mảng rỗng nếu chưa chạy lệnh SQL
    }

    $now = now();
@endphp

<div class="data-container animate-fade-in-up">
    <div class="data-sidebar">
        <div class="data-sidebar-title">DỮ LIỆU SỰ KIỆN</div>
        <div class="data-menu-item active" onclick="window.filterEventTime('all', this)">
            <i class="fa-solid fa-layer-group"></i> Tất cả sự kiện
        </div>
        <div class="data-menu-item" onclick="window.filterEventTime('ongoing', this)">
            <i class="fa-solid fa-clock-rotate-left"></i> Đang diễn ra
        </div>
        <div class="data-menu-item" onclick="window.filterEventTime('ended', this)">
            <i class="fa-solid fa-calendar-check"></i> Đã kết thúc
        </div>
    </div>

    <div class="data-main">
        
        @if(!$hasProofColumns)
            <div style="background: #fce8e6; border: 1px solid #fad2cf; color: #d93025; padding: 15px; border-radius: 8px; margin-bottom: 20px; font-weight: 500;">
                <i class="fa-solid fa-triangle-exclamation"></i> <b>Lỗi Database:</b> Bảng `event_participants` đang thiếu các cột minh chứng. Hãy chạy lệnh ALTER TABLE trong phpMyAdmin để hiển thị dữ liệu.
            </div>
        @endif

        <div class="data-header-controls">
            <div class="data-filters">
                <span class="filter-label">Lọc:</span>
                <select class="filter-select" id="filter-proof" onchange="window.applyDataFilters()">
                    <option value="all">Tất cả minh chứng</option>
                    <option value="approved">Đã duyệt (Hợp lệ)</option>
                    <option value="pending">Đang chờ duyệt</option>
                    <option value="none">Chưa nộp minh chứng</option>
                </select>
                
                <select class="filter-select" id="filter-sort" onchange="window.applyDataFilters()">
                    <option value="desc">Mới nhất trước</option>
                    <option value="asc">Cũ nhất trước</option>
                </select>
            </div>

            <button class="btn-calc-score" onclick="window.calculateTrainingScore()">
                <i class="fa-solid fa-calculator"></i> Tính điểm rèn luyện
            </button>
        </div>

        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 30%;">Tên sự kiện</th>
                    <th style="width: 20%;">Thời gian</th>
                    <th style="width: 15%; text-align: center;">Form nộp</th>
                    <th style="width: 20%; text-align: center;">Minh chứng</th>
                    <th style="width: 15%;">Trạng thái</th>
                </tr>
            </thead>
            <tbody id="event-table-body">
                @forelse($events as $event)
                    @php
                        $start = \Carbon\Carbon::parse($event->start_time);
                        $end = \Carbon\Carbon::parse($event->end_time);
                        $isEnded = $end->isPast();
                        
                        // Xác định class cho trạng thái minh chứng
                        $proofBadge = 'badge-none'; $proofIcon = 'fa-circle-xmark'; $proofText = 'Chưa nộp';
                        if($event->proof_status == 'approved') { $proofBadge = 'badge-approved'; $proofIcon = 'fa-circle-check'; $proofText = 'Đã duyệt'; }
                        elseif($event->proof_status == 'pending') { $proofBadge = 'badge-pending'; $proofIcon = 'fa-clock'; $proofText = 'Chờ duyệt'; }
                        elseif($event->proof_status == 'rejected') { $proofBadge = 'badge-rejected'; $proofIcon = 'fa-triangle-exclamation'; $proofText = 'Từ chối'; }
                    @endphp
                    <tr class="event-row" 
                        data-time-status="{{ $isEnded ? 'ended' : 'ongoing' }}" 
                        data-proof-status="{{ $event->proof_status }}"
                        data-timestamp="{{ $start->timestamp }}">
                        
                        <td style="font-weight: 600;">{{ $event->title }}</td>
                        
                        <td style="color: #65676b; font-size: 13px;">
                            {{ $start->format('d/m/Y') }}<br>
                            <small>{{ $start->format('H:i') }} - {{ $end->format('H:i') }}</small>
                        </td>
                        
                        <td style="text-align: center;">
                            <button class="btn-form" onclick="alert('Mở form nộp minh chứng cho sự kiện #{{ $event->id }}')">Xem Form</button>
                        </td>
                        
                        <td style="text-align: center;">
                            <span class="badge {{ $proofBadge }}"><i class="fa-solid {{ $proofIcon }}"></i> {{ $proofText }}</span>
                        </td>
                        
                        <td>
                            @if($isEnded)
                                <span style="color: #dc3545; font-weight: 600; font-size: 13px;"><span class="status-dot dot-ended"></span> Đã kết thúc</span>
                            @else
                                <span style="color: #23a559; font-weight: 600; font-size: 13px;"><span class="status-dot dot-ongoing"></span> Đang diễn ra</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 40px; color: #8e8e8e;">
                            <i class="fa-regular fa-calendar-xmark" style="font-size: 40px; margin-bottom: 15px; opacity: 0.5;"></i>
                            <p>Bạn chưa có dữ liệu sự kiện nào.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
    // Trạng thái bộ lọc hiện tại
    window.currentFilters = {
        timeStatus: 'all', // all, ongoing, ended
        proofStatus: 'all', // all, approved, pending, none
        sortMode: 'desc'    // desc, asc
    };

    // Hàm chọn Sidebar (Đang diễn ra / Đã kết thúc)
    window.filterEventTime = function(status, element) {
        // Cập nhật UI menu
        document.querySelectorAll('.data-menu-item').forEach(el => el.classList.remove('active'));
        element.classList.add('active');
        
        // Cập nhật state và lọc
        window.currentFilters.timeStatus = status;
        window.applyDataFilters();
    };

    // Hàm áp dụng tất cả các bộ lọc (Topbar + Sidebar) và Sắp xếp
    window.applyDataFilters = function() {
        window.currentFilters.proofStatus = document.getElementById('filter-proof').value;
        window.currentFilters.sortMode = document.getElementById('filter-sort').value;
        
        const tbody = document.getElementById('event-table-body');
        const rows = Array.from(tbody.querySelectorAll('.event-row'));
        
        // 1. Ẩn/Hiện dựa theo Logic
        rows.forEach(row => {
            const timeStat = row.getAttribute('data-time-status');
            const proofStat = row.getAttribute('data-proof-status');
            
            let timeMatch = (window.currentFilters.timeStatus === 'all' || timeStat === window.currentFilters.timeStatus);
            let proofMatch = (window.currentFilters.proofStatus === 'all' || proofStat === window.currentFilters.proofStatus);
            
            row.style.display = (timeMatch && proofMatch) ? 'table-row' : 'none';
        });

        // 2. Sắp xếp lại DOM dựa theo Timestamp
        rows.sort((a, b) => {
            let timeA = parseInt(a.getAttribute('data-timestamp'));
            let timeB = parseInt(b.getAttribute('data-timestamp'));
            return window.currentFilters.sortMode === 'desc' ? (timeB - timeA) : (timeA - timeB);
        });

        // Nạp lại các dòng vào tbody theo thứ tự mới
        rows.forEach(row => tbody.appendChild(row));
    };

    // Nút Tính điểm rèn luyện chờ logic
    window.calculateTrainingScore = function() {
        alert("Chức năng Tính điểm rèn luyện đang được xây dựng!\nHệ thống sẽ thu thập các minh chứng 'Đã duyệt' và áp dụng công thức để tính tổng điểm cho bạn.");
    };
</script>