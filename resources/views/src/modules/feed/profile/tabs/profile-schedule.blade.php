<style>
    .schedule-card-wrapper { background: #fff; border-radius: 20px; padding: 25px; box-shadow: 0 4px 20px rgba(0,0,0,0.04); margin-top: 20px; font-family: 'Inter', sans-serif; animation: fadeInUp 0.3s ease; }
    
    .quick-add-card { background: #f8f9fa; border: 1px solid #e4e6eb; border-radius: 12px; padding: 20px; margin-bottom: 25px; }
    .form-row { display: flex; gap: 15px; margin-top: 10px; flex-wrap: wrap; }
    .form-group { flex: 1; display: flex; flex-direction: column; gap: 5px; min-width: 200px; }
    .form-group label { font-size: 12px; font-weight: 700; color: #65676b; }
    .form-group input { padding: 10px; border: 1px solid #ccd0d5; border-radius: 8px; }
    .btn-add-task { background: #1877f2; color: #fff; border: none; padding: 0 25px; border-radius: 8px; font-weight: 600; cursor: pointer; height: 40px; align-self: flex-end; }

    .pill-filters { display: flex; gap: 10px; overflow-x: auto; padding-bottom: 10px; margin-bottom: 15px; }
    .pill-filters button { background: #f0f2f5; color: #4b4b4b; border: none; padding: 8px 16px; border-radius: 20px; font-weight: 600; font-size: 13px; cursor: pointer; white-space: nowrap; }
    .pill-filters button.active { background: #e7f3ff; color: #1877f2; }

    .date-slider { display: flex; justify-content: space-between; margin-bottom: 30px; overflow-x: auto; gap: 10px; }
    .date-item { display: flex; flex-direction: column; align-items: center; gap: 8px; color: #8e8e8e; cursor: pointer; min-width: 45px; }
    .date-item .d-num { width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; background: #f0f2f5; color: #4b4b4b; border-radius: 10px; font-weight: 700; }
    .date-item.active .d-num { background: #4a66f0; color: white; }

    .t-row { display: flex; align-items: stretch; gap: 15px; margin-bottom: 15px; }
    .t-time-col { width: 50px; color: #ed4245; font-weight: 700; font-size: 13px; padding: 10px 0; text-align: right; }
    .t-card { flex: 1; background: #2f3136; border-radius: 16px; padding: 15px 20px; border-left: 6px solid #1877f2; }
    .t-info .title { color: #fff; font-size: 16px; font-weight: 700; margin-bottom: 5px; }
    .t-info .time { color: #b9bbbe; font-size: 13px; }
</style>

<div class="schedule-card-wrapper" id="master-schedule-container">
    <div class="quick-add-card">
        <div style="font-weight: 700; color: #1c1e21;"><i class="fa-solid fa-calendar-plus"></i> TẠO LỊCH CÁ NHÂN MỚI</div>
        <div class="form-row">
            <div class="form-group">
                <label>Tiêu đề công việc</label>
                <input type="text" id="add-task-title" placeholder="VD: Nộp bài tập nhóm...">
            </div>
            <div class="form-group">
                <label>Ngày & Giờ</label>
                <input type="datetime-local" id="add-task-date">
            </div>
            <button class="btn-add-task" onclick="window.submitNewTaskFromForm()">Tạo lịch</button>
        </div>
    </div>

    <div class="pill-filters" id="ui-pill-filters">
        <button class="active" onclick="window.filterSchedule('all', this)">Tổng hợp</button>
        <button onclick="window.filterSchedule('class', this)">Lịch học</button>
        <button onclick="window.filterSchedule('task', this)">Lịch cá nhân</button>
        <button onclick="window.filterSchedule('event', this)">Sự kiện</button>
    </div>

    <div class="date-slider" id="ui-date-slider"></div>

    <div id="dynamic-timeline">
        <div style="text-align: center; color: #8e8e8e; padding: 20px;">Đang tải...</div>
    </div>
</div>

<script>
    window.scheduleState = { filter: 'all', selectedDate: new Date().toISOString().split('T')[0] };

    function renderDateSlider() {
        const slider = document.getElementById('ui-date-slider');
        const days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
        let html = '';
        let baseDate = new Date(window.scheduleState.selectedDate);
        for(let i = -3; i <= 3; i++) {
            let d = new Date(baseDate);
            d.setDate(baseDate.getDate() + i);
            let dStr = new Date(d.getTime() - (d.getTimezoneOffset() * 60000)).toISOString().split('T')[0];
            let isActive = dStr === window.scheduleState.selectedDate ? 'active' : '';
            html += `<div class="date-item ${isActive}" onclick="window.selectDate('${dStr}')"><div class="d-name">${days[d.getDay()]}</div><div class="d-num">${d.getDate()}</div></div>`;
        }
        slider.innerHTML = html;
    }

    window.selectDate = function(dateStr) { window.scheduleState.selectedDate = dateStr; renderDateSlider(); renderTimeline(); }
    
    window.filterSchedule = function(type, btn) {
        window.scheduleState.filter = type;
        document.querySelectorAll('.pill-filters button').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        renderTimeline();
    }

    function renderTimeline() {
        const container = document.getElementById('dynamic-timeline');
        let scheduleData = window.currentProfileUser.schedule || [];
        
        // 1. Lọc theo ngày
        scheduleData = scheduleData.filter(i => i.start.split('T')[0] === window.scheduleState.selectedDate);
        
        // 2. Lọc theo loại
        if (window.scheduleState.filter !== 'all') {
            scheduleData = scheduleData.filter(i => i.type === window.scheduleState.filter);
        }

        if (scheduleData.length === 0) {
            container.innerHTML = `<div style="text-align: center; color: #8e8e8e; padding: 40px 20px;">Không có lịch trình phù hợp.</div>`;
        } else {
            let html = '';
            scheduleData.forEach(item => {
                html += `
                    <div class="t-row">
                        <div class="t-time-col"><span>${item.timeLabel}</span></div>
                        <div class="t-card" style="border-left-color: ${item.color}">
                            <div class="t-info"><div class="title">${item.title}</div><div class="time">${item.timeDetail} | ${item.location}</div></div>
                        </div>
                    </div>`;
            });
            container.innerHTML = html;
        }
    }

    window.submitNewTaskFromForm = async function() {
        const title = document.getElementById('add-task-title').value;
        const date = document.getElementById('add-task-date').value;
        if (!title || !date) { alert("Thiếu thông tin!"); return; }
        try {
            const response = await fetch(`/profile/${window.currentProfileUser.msv}/tasks`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ title: title, due_date: date })
            });
            if (response.ok) { alert("Đã thêm!"); location.reload(); }
        } catch (error) { alert("Lỗi kết nối!"); }
    };

    renderDateSlider();
    renderTimeline();
</script>