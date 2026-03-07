<style>
    .schedule-card-wrapper { background: #fff; border-radius: 20px; padding: 25px; box-shadow: 0 4px 20px rgba(0,0,0,0.04); margin-top: 20px; font-family: 'Inter', sans-serif; }
    .schedule-header-controls { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 15px; }
    .month-year-picker { display: flex; gap: 10px; align-items: center; }
    .styled-select { padding: 8px 12px; border-radius: 8px; border: 1px solid #ddd; font-weight: 600; color: #1c1e21; outline: none; cursor: pointer; background: #f8f9fa; }
    
    .pill-filters { display: flex; gap: 10px; margin-bottom: 20px; overflow-x: auto; padding-bottom: 5px; }
    .pill-filters button { background: #f0f2f5; color: #4b4b4b; border: none; padding: 8px 16px; border-radius: 20px; font-weight: 600; font-size: 13px; cursor: pointer; transition: 0.2s; white-space: nowrap; }
    .pill-filters button.active { background: #e7f3ff; color: #1877f2; }

    .date-slider { display: flex; gap: 10px; overflow-x: auto; padding-bottom: 10px; margin-bottom: 25px; scroll-behavior: smooth; }
    .date-item { min-width: 60px; height: 75px; display: flex; flex-direction: column; align-items: center; justify-content: center; border-radius: 12px; border: 1px solid #f0f2f5; cursor: pointer; transition: 0.2s; background: #fff; }
    .date-item.active { background: #1877f2; border-color: #1877f2; color: #fff; box-shadow: 0 4px 12px rgba(24, 119, 242, 0.3); }
    .date-item .d-name { font-size: 11px; text-transform: uppercase; font-weight: 700; margin-bottom: 5px; }
    .date-item .d-num { font-size: 18px; font-weight: 800; }

    .t-row { display: flex; gap: 20px; margin-bottom: 20px; align-items: flex-start; }
    .t-time-col { width: 50px; font-size: 13px; font-weight: 700; color: #65676b; padding-top: 15px; text-align: right; }
    .t-card { flex: 1; background: #f8f9fa; border-left: 5px solid #ddd; border-radius: 10px; padding: 15px; display: flex; justify-content: space-between; align-items: center; transition: 0.2s; }
    .t-card.completed { border-left-color: #23a559 !important; background: #f0f9f4; opacity: 0.8; }
    .t-card.completed .title { text-decoration: line-through; color: #65676b; }

    .t-info .title { font-weight: 700; color: #1c1e21; margin-bottom: 4px; }
    .t-info .time { font-size: 12px; color: #65676b; }
    
    .btn-done { background: #fff; border: 1px solid #23a559; color: #23a559; padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 700; cursor: pointer; transition: 0.2s; }
    .btn-done:hover { background: #23a559; color: #fff; }

    .quick-add-card { background: #f8f9fa; border: 1px solid #e4e6eb; border-radius: 12px; padding: 20px; margin-top: 30px; display: none; }

    /* ... (Giữ nguyên CSS cũ) ... */
    .quick-add-card { background: #f8f9fa; border: 1px solid #e4e6eb; border-radius: 12px; padding: 20px; margin-top: 30px; display: none; }
    #form-title-text { font-weight: 700; color: #1c1e21; margin-bottom: 15px; text-transform: uppercase; font-size: 13px; }
</style>

@php
    // 1. Lấy Sự kiện (Cố định)
    $events = DB::table('event_participants')
        ->join('events', 'event_participants.event_id', '=', 'events.id')
        ->where('event_participants.user_id', $user->id)
        ->select('events.title', 'events.start_time', 'events.end_time', 'events.location')
        ->get()->map(fn($e) => [
            'type' => 'event', 'title' => $e->title,
            'start' => date('Y-m-d', strtotime($e->start_time)),
            'timeLabel' => date('H:i', strtotime($e->start_time)),
            'timeDetail' => date('H:i', strtotime($e->start_time)).' - '.date('H:i', strtotime($e->end_time)),
            'location' => $e->location, 'color' => '#f5a623', 'is_done' => false
        ]);

    // 2. Lấy Task (Cá nhân & Học tập)
    $tasks = DB::table('user_tasks')->where('user_id', $user->id)->get()->map(fn($t) => [
        'type' => $t->type ?? 'task', // Lấy type từ DB
        'id' => $t->id, 'title' => $t->title,
        'start' => date('Y-m-d', strtotime($t->due_date)),
        'timeLabel' => date('H:i', strtotime($t->due_date)),
        'timeDetail' => date('H:i', strtotime($t->due_date)) . ($t->type == 'class' ? ' (Lịch học thêm)' : ' (Cá nhân)'),
        'location' => $t->type == 'class' ? 'Phòng học/Online' : 'Việc riêng', 
        'color' => $t->type == 'class' ? '#23a559' : '#ed4245', 
        'is_done' => $t->is_completed
    ]);

    // 3. Lịch học lặp lại (Mock data)
    $recurringClasses = collect([
        ['type' => 'class', 'title' => 'Lập trình Web (Định kỳ)', 'day_of_week' => 2, 'timeLabel' => '07:30', 'timeDetail' => '07:30 - 10:00', 'location' => 'P.402-D2', 'color' => '#23a559'],
        ['type' => 'class', 'title' => 'Cơ sở dữ liệu (Định kỳ)', 'day_of_week' => 4, 'timeLabel' => '13:00', 'timeDetail' => '13:00 - 15:30', 'location' => 'P.305-D2', 'color' => '#23a559'],
    ]);

    $dbData = $events->concat($tasks)->toJson();
    $mockClasses = $recurringClasses->toJson();
@endphp

<div class="schedule-card-wrapper">
    <div class="schedule-header-controls">
        <div class="month-year-picker">
            <select id="sel-month" class="styled-select" onchange="window.updateScheduleUI()"></select>
            <select id="sel-year" class="styled-select" onchange="window.updateScheduleUI()"></select>
        </div>
        <div class="pill-filters">
            <button class="active" onclick="window.setSchFilter('all', this)">Tổng hợp</button>
            <button onclick="window.setSchFilter('class', this)">Lịch học</button>
            <button onclick="window.setSchFilter('task', this)">Lịch cá nhân</button>
            <button onclick="window.setSchFilter('event', this)">Sự kiện</button>
        </div>
    </div>

    <div class="date-slider" id="ui-date-slider"></div>
    <div id="dynamic-timeline"></div>

    <div class="quick-add-card" id="quick-add-section">
        <div id="form-title-text"><i class="fa-solid fa-plus-circle"></i> TẠO LỊCH MỚI</div>
        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <input type="text" id="new-task-title" placeholder="..." style="flex: 2; padding: 10px; border-radius: 8px; border: 1px solid #ddd;">
            <input type="datetime-local" id="new-task-date" style="flex: 1; padding: 10px; border-radius: 8px; border: 1px solid #ddd;">
            <button id="btn-save-task" onclick="window.handleSaveTask(this)" style="background: #1877f2; color: #fff; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 600; cursor: pointer;">Lưu</button>
        </div>
    </div>
</div>

<script>
    (function() {
        window.schData = {!! $dbData !!};
        window.recurringClasses = {!! $mockClasses !!};
        
        const now = new Date();
        const state = {
            month: now.getMonth(),
            year: now.getFullYear(),
            selectedDate: now.toLocaleDateString('en-CA'),
            filter: 'all'
        };

        window.updateScheduleUI = function() {
            state.month = parseInt(document.getElementById('sel-month').value);
            state.year = parseInt(document.getElementById('sel-year').value);
            state.selectedDate = new Date(state.year, state.month, 1).toLocaleDateString('en-CA');
            renderSlider(); renderTimeline();
        };

        window.setSchFilter = function(f, btn) {
            state.filter = f;
            document.querySelectorAll('.pill-filters button').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            
            // Hiện form cho Lịch học (class) và Cá nhân (task)
            const addSection = document.getElementById('quick-add-section');
            const titleText = document.getElementById('form-title-text');
            const inputTitle = document.getElementById('new-task-title');

            if (f === 'task' || f === 'class') {
                addSection.style.display = 'block';
                titleText.innerHTML = f === 'class' ? '<i class="fa-solid fa-graduation-cap"></i> TẠO LỊCH HỌC BỔ SUNG' : '<i class="fa-solid fa-user"></i> TẠO LỊCH CÁ NHÂN MỚI';
                inputTitle.placeholder = f === 'class' ? 'VD: Học bù, Lịch thi, Họp nhóm...' : 'VD: Đi mua đồ, Tập gym...';
            } else {
                addSection.style.display = 'none';
            }
            
            renderTimeline();
        };

        function renderSlider() {
            const slider = document.getElementById('ui-date-slider');
            const daysInMonth = new Date(state.year, state.month + 1, 0).getDate();
            const dayNames = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
            let html = '';
            for (let i = 1; i <= daysInMonth; i++) {
                const d = new Date(state.year, state.month, i);
                const dStr = d.toLocaleDateString('en-CA');
                html += `<div class="date-item ${dStr === state.selectedDate ? 'active' : ''}" onclick="window.selectSchDate('${dStr}')">
                    <div class="d-name">${dayNames[d.getDay()]}</div>
                    <div class="d-num">${i}</div>
                </div>`;
            }
            slider.innerHTML = html;
            const active = slider.querySelector('.active');
            if(active) active.scrollIntoView({ behavior: 'smooth', inline: 'center', block: 'nearest' });
        }

        window.selectSchDate = function(d) { state.selectedDate = d; renderSlider(); renderTimeline(); };

        function renderTimeline() {
            const container = document.getElementById('dynamic-timeline');
            const d = new Date(state.selectedDate);
            const dbDay = d.getDay() === 0 ? 1 : d.getDay() + 1;

            const dayClasses = window.recurringClasses.filter(c => c.day_of_week === dbDay);
            let filtered = [];

            if (state.filter === 'all') {
                filtered = window.schData.filter(i => i.start === state.selectedDate).concat(dayClasses);
            } else if (state.filter === 'class') {
                // Lịch học = Môn định kỳ + Task có type là 'class'
                const extraClasses = window.schData.filter(i => i.start === state.selectedDate && i.type === 'class');
                filtered = dayClasses.concat(extraClasses);
            } else {
                filtered = window.schData.filter(i => i.start === state.selectedDate && i.type === state.filter);
            }

            filtered.sort((a, b) => a.timeLabel.localeCompare(b.timeLabel));

            if(filtered.length === 0) {
                container.innerHTML = `<div style="text-align:center; padding:40px; color:#8e8e8e; border:1px dashed #ddd; border-radius:12px;">Trống</div>`;
                return;
            }

            container.innerHTML = filtered.map(item => `
                <div class="t-row">
                    <div class="t-time-col">${item.timeLabel}</div>
                    <div class="t-card ${item.is_done ? 'completed' : ''}" style="border-left-color: ${item.color}">
                        <div class="t-info">
                            <div class="title">${item.title}</div>
                            <div class="time">${item.timeDetail} | ${item.location}</div>
                        </div>
                        ${(item.type === 'task' || (item.type === 'class' && item.id)) ? `
                            <button class="btn-done" onclick="window.toggleTaskStatus(${item.id})">
                                ${item.is_done ? 'Hoàn tác' : 'Xong'}
                            </button>
                        ` : ''}
                    </div>
                </div>
            `).join('');
        }

        window.handleSaveTask = async function(btn) {
            const title = document.getElementById('new-task-title').value;
            const date = document.getElementById('new-task-date').value;
            if(!title || !date) return alert("Thiếu thông tin!");
            btn.disabled = true; btn.innerText = "...";
            try {
                const response = await fetch(`/profile/${window.studentCode}/tasks`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ title, due_date: date, type: state.filter }) // Gửi kèm type là 'task' hoặc 'class'
                });
                if(response.ok) window.switchProfileTab('schedule');
            } catch(e) { alert("Lỗi!"); btn.disabled = false; btn.innerText = "Lưu"; }
        };

        window.toggleTaskStatus = async function(taskId) {
            try {
                const response = await fetch(`/profile/${window.studentCode}/tasks/${taskId}/toggle`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                });
                if(response.ok) window.switchProfileTab('schedule');
            } catch(e) { alert("Lỗi!"); }
        };

        const mSel = document.getElementById('sel-month');
        const ySel = document.getElementById('sel-year');
        const months = ["Tháng 1", "Tháng 2", "Tháng 3", "Tháng 4", "Tháng 5", "Tháng 6", "Tháng 7", "Tháng 8", "Tháng 9", "Tháng 10", "Tháng 11", "Tháng 12"];
        mSel.innerHTML = months.map((m, i) => `<option value="${i}" ${i === state.month ? 'selected' : ''}>${m}</option>`).join('');
        ySel.innerHTML = [state.year-1, state.year, state.year+1].map(y => `<option value="${y}" ${y === state.year ? 'selected' : ''}>Năm ${y}</option>`).join('');

        renderSlider(); renderTimeline();
    })();
</script>