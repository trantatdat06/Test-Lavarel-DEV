<style>
    /* CSS Giữ nguyên như bản thiết kế Dark Mode cực đẹp của bạn */
    .schedule-card-wrapper { background: #fff; border-radius: 20px; padding: 25px; box-shadow: 0 4px 20px rgba(0,0,0,0.04); margin-top: 20px; font-family: 'Inter', sans-serif; animation: fadeInUp 0.3s ease; }
    .pill-filters { display: flex; gap: 10px; overflow-x: auto; padding-bottom: 10px; margin-bottom: 15px; }
    .pill-filters button { background: #f0f2f5; color: #4b4b4b; border: none; padding: 8px 16px; border-radius: 20px; font-weight: 600; font-size: 13px; cursor: pointer; white-space: nowrap; transition: 0.2s; }
    .pill-filters button.active { background: #e7f3ff; color: #1877f2; }
    .view-toggles { display: flex; background: #65676b; padding: 5px; border-radius: 12px; margin-bottom: 20px; }
    .view-toggles button { flex: 1; border: none; background: transparent; padding: 10px; border-radius: 8px; font-weight: 600; cursor: pointer; color: #d0d0d0; font-size: 14px; transition: 0.3s; }
    .view-toggles button.active { background: #4a66f0; color: #fff; box-shadow: 0 2px 5px rgba(0,0,0,0.2); }
    .date-slider { display: flex; justify-content: space-between; margin-bottom: 30px; overflow-x: auto; gap: 10px; padding-bottom: 10px; scrollbar-width: none; }
    .date-item { display: flex; flex-direction: column; align-items: center; gap: 8px; color: #8e8e8e; cursor: pointer; min-width: 45px; }
    .date-item .d-name { font-size: 12px; font-weight: 600; }
    .date-item .d-num { width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; background: #f0f2f5; color: #4b4b4b; border-radius: 10px; font-weight: 700; font-size: 14px; transition: 0.2s;}
    .date-item.active .d-name { color: #4a66f0; }
    .date-item.active .d-num { background: #4a66f0; color: white; box-shadow: 0 4px 10px rgba(74, 102, 240, 0.3); transform: translateY(-2px); }
    .timeline-list { display: flex; flex-direction: column; gap: 20px; }
    .t-row { display: flex; align-items: stretch; gap: 15px; }
    .t-time-col { width: 50px; flex-shrink: 0; display: flex; flex-direction: column; align-items: flex-end; justify-content: space-between; color: #ed4245; font-weight: 700; font-size: 13px; padding: 10px 0; }
    .t-card { flex: 1; background: #2f3136; border-radius: 16px; position: relative; overflow: hidden; display: flex; flex-direction: column; }
    .t-card-border { position: absolute; left: 0; top: 0; bottom: 0; width: 6px; border-radius: 16px 0 0 16px; }
    .t-card-content { padding: 15px 20px 15px 25px; display: flex; justify-content: space-between; align-items: center; }
    .t-info .title { color: #fff; font-size: 16px; font-weight: 700; margin-bottom: 5px; }
    .t-info .time { color: #b9bbbe; font-size: 13px; }
    .t-actions { color: #b9bbbe; cursor: pointer; padding: 5px; position: relative; }
    .btn-schedule-event { background: #4a66f0; color: white; border: none; padding: 12px; border-radius: 10px; width: calc(100% - 40px); margin: 0 auto 15px; font-weight: 600; font-size: 14px; cursor: pointer; transition: 0.2s; box-shadow: 0 4px 15px rgba(74, 102, 240, 0.4); }
    .btn-schedule-event:hover { background: #3b55d9; }
    .modal-overlay { position: fixed !important; top: 0 !important; left: 0 !important; right: 0 !important; bottom: 0 !important; background: rgba(0,0,0,0.6) !important; z-index: 999999 !important; display: none; align-items: center; justify-content: center; }
    .modal-overlay.show { display: flex !important; }
    .modal-content { background: white; padding: 25px; border-radius: 16px; width: 400px; max-width: 90%; box-shadow: 0 10px 30px rgba(0,0,0,0.2); }
    .form-group { margin-bottom: 15px; display: flex; flex-direction: column; gap: 6px; }
    .form-group label { font-size: 13px; font-weight: 600; color: #65676b; }
    .form-group input { padding: 12px; border: 1px solid #e4e6eb; border-radius: 8px; font-family: inherit; font-size: 14px; outline: none;}
    .action-dropdown { position: absolute; right: 0; top: 25px; background: white; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.2); display: none; flex-direction: column; z-index: 100; width: 150px; overflow: hidden; }
    .action-dropdown.show { display: flex; }
    .action-dropdown button { padding: 12px 15px; border: none; background: white; text-align: left; font-size: 13px; cursor: pointer; border-bottom: 1px solid #f0f2f5; font-weight: 600; color: #1c1e21; }
    .action-dropdown button:hover { background: #f8f9fa; color: #4a66f0; }
</style>

<div class="schedule-card-wrapper" id="master-schedule-container">
    <div class="pill-filters" id="ui-pill-filters">
        <button class="active" data-filter="all">Tổng hợp</button>
        <button data-filter="event">Lịch đăng ký</button>
        <button data-filter="class">Lịch học</button>
        <button data-filter="task">Kế hoạch cá nhân</button>
    </div>

    <div class="view-toggles" id="ui-view-toggles">
        <button data-view="Day">Day</button>
        <button class="active" data-view="Week">Week</button>
        <button data-view="Month">Month</button>
    </div>

    <div class="date-slider" id="ui-date-slider"></div>

    <div class="timeline-list" id="dynamic-timeline">
        <div style="text-align: center; color: #8e8e8e; padding: 20px;">Đang tải lịch trình...</div>
    </div>
</div>

<div id="createTaskModal" class="modal-overlay">
    <div class="modal-content">
        <div style="font-size: 18px; font-weight: 700; margin-bottom: 20px; display: flex; justify-content: space-between;">
            Tạo Lịch Cá Nhân mới
            <i class="fa-solid fa-xmark js-close-modal" data-target="createTaskModal" style="cursor:pointer; color:#8e8e8e;"></i>
        </div>
        <div class="form-group">
            <label>Tiêu đề công việc / Nhắc nhở</label>
            <input type="text" id="task-title" placeholder="VD: Ôn thi Tiếng Anh, Chạy deadline...">
        </div>
        <div class="form-group">
            <label>Ngày giờ thực hiện</label>
            <input type="datetime-local" id="task-datetime">
        </div>
        <button id="btn-submit-task" style="background: #4a66f0; color: white; border: none; padding: 12px; border-radius: 8px; font-weight: 600; cursor: pointer; width: 100%; margin-top: 10px;">
            <i class="fa-solid fa-plus"></i> Thêm vào lịch trình
        </button>
    </div>
</div>

<script>
    // 1. Quản lý Trạng thái 
    window.scheduleState = {
        filter: 'all', 
        view: 'Week', 
        selectedDate: new Date().toISOString().split('T')[0] 
    };

    function renderDateSlider() {
        const slider = document.getElementById('ui-date-slider');
        const days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
        let html = '';
        let baseDate = new Date(window.scheduleState.selectedDate);
        let daysToShow = window.scheduleState.view === 'Day' ? 3 : (window.scheduleState.view === 'Month' ? 14 : 7);
        let startOffset = Math.floor(daysToShow / 2) * -1; 
        
        for(let i = startOffset; i < startOffset + daysToShow; i++) {
            let d = new Date(baseDate);
            d.setDate(baseDate.getDate() + i);
            let dStr = new Date(d.getTime() - (d.getTimezoneOffset() * 60000)).toISOString().split('T')[0];
            let isActive = dStr === window.scheduleState.selectedDate ? 'active' : '';
            html += `
                <div class="date-item ${isActive} js-select-date" data-date="${dStr}">
                    <div class="d-name">${days[d.getDay()]}</div>
                    <div class="d-num">${d.getDate()}</div>
                </div>
            `;
        }
        slider.innerHTML = html;
    }

    function renderTimeline() {
        const container = document.getElementById('dynamic-timeline');
        let scheduleData = window.currentProfileUser.schedule || [];

        // Lọc theo LoạI và Ngày
        if (window.scheduleState.filter !== 'all') {
            scheduleData = scheduleData.filter(i => window.scheduleState.filter === i.type);
        }
        scheduleData = scheduleData.filter(i => i.start && i.start.split('T')[0] === window.scheduleState.selectedDate);

        let html = `<button class="btn-schedule-event js-open-modal" data-target="createTaskModal">Schedule an event / Tạo lịch mới</button>`;
        
        if (scheduleData.length === 0) {
            html += `<div style="text-align: center; color: #8e8e8e; padding: 40px 20px;"><i class="fa-regular fa-calendar-xmark" style="font-size: 30px; margin-bottom: 10px;"></i><br>Không có lịch trình.</div>`;
        } else {
            scheduleData.forEach(item => {
                html += `
                    <div class="t-row">
                        <div class="t-time-col"><span>${item.timeLabel}</span></div>
                        <div class="t-card">
                            <div class="t-card-border" style="background: ${item.color};"></div>
                            <div class="t-card-content">
                                <div class="t-info">
                                    <div class="title">${item.title}</div>
                                    <div class="time">${item.timeDetail}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });
        }
        container.innerHTML = html;
        
        // Đưa Modal ra <body> an toàn
        const m = document.getElementById('createTaskModal');
        if(m && m.parentNode.tagName !== 'BODY') document.body.appendChild(m);
    }

    // =========================================================
    // HỆ THỐNG BẮT SỰ KIỆN "BẤT TỬ" (Event Delegation)
    // =========================================================
    document.getElementById('master-schedule-container').addEventListener('click', function(e) {
        // Nút mở Modal Tạo Lịch
        if (e.target.closest('.js-open-modal')) {
            const target = e.target.closest('.js-open-modal').getAttribute('data-target');
            document.getElementById(target).style.display = 'flex';
        }
        // Chọn Ngày trên thanh trượt
        if (e.target.closest('.js-select-date')) {
            window.scheduleState.selectedDate = e.target.closest('.js-select-date').getAttribute('data-date');
            renderDateSlider(); renderTimeline();
        }
        // Chọn View (Day/Week)
        if (e.target.closest('#ui-view-toggles button')) {
            const btn = e.target.closest('#ui-view-toggles button');
            document.querySelectorAll('#ui-view-toggles button').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            window.scheduleState.view = btn.getAttribute('data-view');
            renderDateSlider();
        }
        // Chọn Filter
        if (e.target.closest('#ui-pill-filters button')) {
            const btn = e.target.closest('#ui-pill-filters button');
            document.querySelectorAll('#ui-pill-filters button').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            window.scheduleState.filter = btn.getAttribute('data-filter');
            renderTimeline();
        }
    });

    // Sự kiện Đóng Modal
    document.addEventListener('click', function(e) {
        if (e.target.closest('.js-close-modal')) {
            const target = e.target.closest('.js-close-modal').getAttribute('data-target');
            document.getElementById(target).style.display = 'none';
        }
    });

    // API TẠO LỊCH (TASK)
    document.getElementById('btn-submit-task').addEventListener('click', async function() {
        const title = document.getElementById('task-title').value;
        const datetime = document.getElementById('task-datetime').value;
        const studentCode = window.currentProfileUser.msv;

        if (!title || !datetime) { alert("Vui lòng điền đủ Tiêu đề và Thời gian!"); return; }

        this.innerText = "Đang lưu..."; this.disabled = true;

        try {
            const response = await fetch(`/profile/${studentCode}/tasks`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ title: title, due_date: datetime })
            });
            if (response.ok) {
                alert("Đã thêm Lịch cá nhân thành công!");
                document.getElementById('createTaskModal').style.display = 'none';
                location.reload(); 
            } else alert("Hệ thống từ chối lưu lịch!");
        } catch (error) { 
            console.error(error); alert("Lỗi kết nối máy chủ."); 
        } finally {
            this.innerHTML = `<i class="fa-solid fa-plus"></i> Thêm vào lịch trình`; this.disabled = false;
        }
    });

    // Chạy mặc định
    renderDateSlider();
    renderTimeline();
</script>