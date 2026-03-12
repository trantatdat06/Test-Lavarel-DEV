<style>
    .widget-events {
        background: #fff; border-radius: 12px; padding: 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05); border: 1px solid #e4e6eb;
        font-family: system-ui, -apple-system, sans-serif; margin-bottom: 15px;
    }
    .we-header {
        display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;
    }
    .we-title { font-weight: 800; color: #1c1e21; font-size: 15px; text-transform: uppercase; }
    .we-view-all { font-size: 13px; color: #1877f2; font-weight: 600; text-decoration: none; cursor: pointer; }
    .we-view-all:hover { text-decoration: underline; }

    .we-item { display: flex; gap: 12px; margin-bottom: 15px; }
    .we-item:last-child { margin-bottom: 0; }
    
    .we-date-box {
        background: #f0f2f5; border-radius: 8px; width: 45px; height: 50px;
        display: flex; flex-direction: column; align-items: center; justify-content: center; flex-shrink: 0;
    }
    .we-month { font-size: 10px; font-weight: 800; color: #dc3545; text-transform: uppercase; }
    .we-day { font-size: 18px; font-weight: 800; color: #1c1e21; line-height: 1; margin-top: 2px; }
    
    .we-content { flex: 1; display: flex; flex-direction: column; justify-content: center; }
    .we-name { font-size: 14px; font-weight: 700; color: #1c1e21; line-height: 1.3; margin-bottom: 4px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
    .we-meta { font-size: 12px; color: #65676b; display: flex; align-items: center; gap: 5px; }
</style>

@php
    // Truy vấn lấy 3 sự kiện chung sắp diễn ra (chưa kết thúc)
    $upcomingGlobalEvents = \Illuminate\Support\Facades\DB::table('events')
        ->where('start_time', '>=', now())
        ->orderBy('start_time', 'asc')
        ->limit(3)
        ->get();
@endphp

<div class="widget-events">
    <div class="we-header">
        <div class="we-title">Sự kiện sắp tới</div>
        <a class="we-view-all" onclick="alert('Chuyển đến trang Khám phá sự kiện!')">Xem tất cả</a>
    </div>

    @forelse($upcomingGlobalEvents as $event)
        @php
            $dateObj = \Carbon\Carbon::parse($event->start_time);
        @endphp
        <div class="we-item">
            <div class="we-date-box">
                <div class="we-month">Thg {{ $dateObj->format('m') }}</div>
                <div class="we-day">{{ $dateObj->format('d') }}</div>
            </div>
            <div class="we-content">
                <div class="we-name" title="{{ $event->title }}">{{ $event->title }}</div>
                <div class="we-meta">
                    <i class="fa-solid fa-location-dot"></i> {{ $event->location ?? 'Học viện Ngân hàng' }}
                </div>
            </div>
        </div>
    @empty
        <div style="font-size: 13px; color: #8e8e8e; text-align: center; padding: 10px 0;">Hiện chưa có sự kiện nào mới.</div>
    @endforelse
</div>