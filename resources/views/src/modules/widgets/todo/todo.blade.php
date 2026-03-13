<style>
    .widget-todo-container {
        background: #fff;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        margin-bottom: 15px;
        border: 1px solid #e4e6eb;
    }
    
    /* Header: Today & Clock */
    .wt-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 20px;
        border-bottom: 2px solid #f0f2f5;
        padding-bottom: 12px;
    }
    .wt-today-label {
        font-size: 16px;
        font-weight: 800;
        color: #1c1e21;
        margin-bottom: 2px;
    }
    .wt-date-label {
        font-size: 13px;
        color: #65676b;
        font-weight: 500;
    }
    .wt-clock-wrapper {
        text-align: right;
    }
    .wt-now-label {
        font-size: 11px;
        font-weight: 700;
        color: #1877f2;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 2px;
    }
    .wt-time-display {
        font-size: 14px;
        font-weight: 700;
        color: #1c1e21;
    }

    /* Sections */
    .wt-section {
        margin-bottom: 20px;
    }
    .wt-section:last-child {
        margin-bottom: 0;
    }
    .wt-sec-title {
        font-size: 14px;
        font-weight: 700;
        color: #1c1e21;
        margin-bottom: 12px;
        text-transform: capitalize;
    }

    /* Event Card */
    .wt-event-card {
        background: #f8f9fa;
        border: 1px solid #e4e6eb;
        border-left: 4px solid #f5a623;
        border-radius: 8px;
        padding: 12px;
        margin-bottom: 10px;
        transition: 0.2s;
    }
    .wt-event-card:hover {
        background: #fff;
        box-shadow: 0 2px 6px rgba(0,0,0,0.05);
        border-color: #ccd0d5;
    }
    .wt-event-title {
        font-size: 13.5px;
        font-weight: 700;
        color: #1c1e21;
        margin-bottom: 6px;
        line-height: 1.4;
    }
    .wt-event-meta {
        font-size: 12px;
        color: #65676b;
        display: flex;
        align-items: center;
        gap: 6px;
        margin-bottom: 4px;
    }

    /* Task List */
    .wt-task-item {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        margin-bottom: 12px;
        padding: 4px 0;
    }
    .wt-task-checkbox {
        width: 18px;
        height: 18px;
        margin-top: 1px;
        cursor: pointer;
        accent-color: #1877f2;
    }
    .wt-task-content {
        flex: 1;
    }
    .wt-task-title {
        font-size: 13.5px;
        color: #1c1e21;
        font-weight: 500;
        line-height: 1.4;
        transition: 0.2s;
    }
    .wt-task-time {
        font-size: 11px;
        color: #8e8e8e;
        margin-top: 2px;
    }
    .wt-task-item.completed .wt-task-title {
        text-decoration: line-through;
        color: #8e8e8e;
    }
</style>

@php
    // Lấy thông tin User hiện tại (Giả định đang dùng Auth hoặc User đầu tiên để test)
    $user = auth()->user() ?? \App\Models\User::first();
    $studentCode = $user->student_code;

    // 1. Kế thừa Dữ liệu Sự kiện (Lấy các sự kiện trong tuần này)
    $upcomingEvents = \Illuminate\Support\Facades\DB::table('event_participants')
        ->join('events', 'event_participants.event_id', '=', 'events.id')
        ->where('event_participants.user_id', $user->id)
        ->where('event_participants.status', 'going')
        ->whereDate('events.start_time', '>=', now()->toDateString())
        ->orderBy('events.start_time', 'asc')
        ->limit(2)
        ->select('events.id', 'events.title', 'events.start_time', 'events.location')
        ->get();

    // 2. Kế thừa Dữ liệu Lịch trình (Lấy các việc CẦN LÀM hôm nay hoặc đang quá hạn)
    $myTasks = \Illuminate\Support\Facades\DB::table('user_tasks')
        ->where('user_id', $user->id)
        ->where('type', 'task')
        ->where(function($q) {
            $q->whereDate('due_date', '<=', now()->toDateString())
              ->orWhere('is_completed', 0);
        })
        ->orderBy('is_completed', 'asc')
        ->orderBy('due_date', 'asc')
        ->limit(5)
        ->get();
@endphp

<div class="widget-todo-container">
    <div class="wt-header">
        <div class="wt-head-left">
            <div class="wt-today-label">To day</div>
            <div class="wt-date-label">{{ now()->format('l, d M') }}</div>
        </div>
        <div class="wt-clock-wrapper">
            <div class="wt-now-label">Now</div>
            <div class="wt-time-display" id="wt-realtime-clock">{{ now()->format('h:i A') }}</div>
        </div>
    </div>

    <div class="wt-section">
        <div class="wt-sec-title">Upcoming event</div>
        
        @forelse($upcomingEvents as $event)
            @php $start = \Carbon\Carbon::parse($event->start_time); @endphp
            <div class="wt-event-card">
                <div class="wt-event-title">
                    <i class="fa-regular fa-calendar-check" style="color: #f5a623;"></i> 
                    {{ $event->title }}
                </div>
                <div class="wt-event-meta">
                    <i class="fa-regular fa-clock"></i> {{ $start->format('d/m/Y - H:i') }}
                </div>
                @if($event->location)
                    <div class="wt-event-meta">
                        <i class="fa-solid fa-location-dot"></i> {{ $event->location }}
                    </div>
                @endif
            </div>
        @empty
            <div style="font-size: 13px; color: #8e8e8e; font-style: italic;">Không có sự kiện nào sắp tới.</div>
        @endforelse
    </div>

    <div class="wt-section">
        <div class="wt-sec-title">My task</div>
        
        @forelse($myTasks as $task)
            <div class="wt-task-item {{ $task->is_completed ? 'completed' : '' }}" id="wt-task-{{ $task->id }}">
                <input type="checkbox" class="wt-task-checkbox" 
                       {{ $task->is_completed ? 'checked' : '' }}
                       onchange="window.widgetToggleTask('{{ $studentCode }}', {{ $task->id }}, this)">
                <div class="wt-task-content">
                    <div class="wt-task-title">{{ $task->title }}</div>
                    @if($task->due_date)
                        <div class="wt-task-time">{{ \Carbon\Carbon::parse($task->due_date)->format('d/m/Y H:i') }}</div>
                    @endif
                </div>
            </div>
        @empty
            <div style="font-size: 13px; color: #8e8e8e; font-style: italic;">Bạn đã hoàn thành mọi công việc!</div>
        @endforelse
    </div>
</div>

<script>
    // 1. Chạy đồng hồ Real-time (cập nhật mỗi phút)
    function updateWidgetClock() {
        const now = new Date();
        let hours = now.getHours();
        let minutes = now.getMinutes();
        const ampm = hours >= 12 ? 'PM' : 'AM';
        hours = hours % 12;
        hours = hours ? hours : 12; // 0 giờ thành 12
        minutes = minutes < 10 ? '0' + minutes : minutes;
        const strTime = hours + ':' + minutes + ' ' + ampm;
        
        const clockEl = document.getElementById('wt-realtime-clock');
        if (clockEl) clockEl.innerText = strTime;
    }
    // Cập nhật ngay lần đầu và thiết lập interval
    updateWidgetClock();
    setInterval(updateWidgetClock, 60000);

    // 2. Gọi API để check/uncheck công việc trực tiếp từ Widget
    window.widgetToggleTask = async function(studentCode, taskId, checkbox) {
        const taskItem = document.getElementById(`wt-task-${taskId}`);
        
        // Cập nhật UI ngay lập tức cho mượt
        if (checkbox.checked) {
            taskItem.classList.add('completed');
        } else {
            taskItem.classList.remove('completed');
        }

        try {
            // Tái sử dụng lại API toggle của module Schedule
            const response = await fetch(`/profile/${studentCode}/tasks/${taskId}/toggle`, {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}' 
                }
            });
            
            if (!response.ok) {
                // Nếu lỗi, hoàn tác UI
                checkbox.checked = !checkbox.checked;
                taskItem.classList.toggle('completed');
                alert("Lỗi máy chủ, không thể cập nhật trạng thái!");
            }
        } catch (error) {
            checkbox.checked = !checkbox.checked;
            taskItem.classList.toggle('completed');
            console.error("Lỗi kết nối:", error);
        }
    };
</script>