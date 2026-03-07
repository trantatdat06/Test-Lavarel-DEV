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
    .t-card { flex: 1; background: #f8f9fa; border-left: 5px solid #ddd; border-radius: 12px; padding: 15px; display: flex; flex-direction: column; gap: 10px; transition: 0.2s; }
    .t-card.completed { border-left-color: #23a559 !important; background: #f0f9f4; opacity: 0.8; }
    .t-card.completed .title { text-decoration: line-through; color: #65676b; }

    .t-header { display: flex; justify-content: space-between; align-items: flex-start; width: 100%; }
    .t-info .title { font-weight: 700; color: #1c1e21; margin-bottom: 4px; }
    .t-info .time { font-size: 12px; color: #65676b; }
    .t-desc { font-size: 13px; color: #65676b; background: #fff; padding: 8px; border-radius: 6px; border: 1px solid #eee; margin-top: 5px; }

    .t-footer-actions { display: flex; gap: 10px; margin-top: 10px; border-top: 1px solid #eee; padding-top: 10px; align-items: center; flex-wrap: wrap;}
    .btn-action-small { padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 700; cursor: pointer; border: 1px solid transparent; transition: 0.2s; background: #fff; }
    .btn-delete { color: #dc3545; border-color: #dc3545; }
    .btn-delete:hover { background: #dc3545; color: #fff; }
    .btn-proof { color: #1877f2; border-color: #1877f2; }
    .btn-proof:hover { background: #1877f2; color: #fff; }
    
    .btn-done { background: #fff; border: 1px solid #23a559; color: #23a559; padding: 6px 12px; border-radius: 6px; font-size: 11px; font-weight: 700; cursor: pointer; transition: 0.2s; }
    .btn-done:hover { background: #23a559; color: #fff; }

    .quick-add-card { background: #f8f9fa; border: 1px solid #e4e6eb; border-radius: 12px; padding: 20px; margin-top: 30px; display: none; }
    .t-row.removing { transform: translateX(100px); opacity: 0; transition: 0.4s; }

    /* Modal xem lịch sử nộp */
    .proof-modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; display: none; align-items: center; justify-content: center; }
    .proof-modal-content { background: #fff; padding: 25px; border-radius: 16px; width: 90%; max-width: 450px; max-height: 80vh; overflow-y: auto; box-shadow: 0 10px 30px rgba(0,0,0,0.2); }
    .proof-version-item { border: 1px solid #eee; border-radius: 8px; padding: 15px; margin-bottom: 15px; background: #fafafa; }
</style>

@php
    $rawTasks = DB::table('user_tasks')->where('user_id', $user->id)->get();
    $taskIds = $rawTasks->pluck('id')->toArray();
    $allProofs = DB::table('task_proofs')->whereIn('user_task_id', $taskIds)->orderBy('version', 'desc')->get()->groupBy('user_task_id');

    $events = DB::table('event_participants')
        ->join('events', 'event_participants.event_id', '=', 'events.id')
        ->where('event_participants.user_id', $user->id)
        ->select('events.id', 'events.title', 'events.start_time', 'events.end_time', 'events.location')
        ->get()->map(fn($e) => [
            'type' => 'event', 'id' => $e->id, 'title' => $e->title,
            'start' => date('Y-m-d', strtotime($e->start_time)), 'timeLabel' => date('H:i', strtotime($e->start_time)),
            'location' => $e->location, 'color' => '#f5a623'
        ]);

    $tasks = $rawTasks->map(function($t) use ($allProofs) {
        $pList = $allProofs->get($t->id, collect());
        return [
            'type' => $t->type ?? 'task', 'id' => $t->id, 'title' => $t->title, 'desc' => $t->description,
            'completion_type' => $t->completion_type ?? 'simple',
            'start' => date('Y-m-d', strtotime($t->due_date)), 'timeLabel' => date('H:i', strtotime($t->due_date)),
            'color' => $t->type == 'class' ? '#23a559' : '#ed4245', 'is_done' => $t->is_completed,
            'proofs' => $pList->toArray()
        ];
    });

    $recurring = DB::table('user_classes')->where('user_id', $user->id)->get()->map(fn($c) => [
        'type' => 'class', 'id' => $c->id, 'title' => $c->title, 
        'day_of_week' => $c->day_of_week,
        'timeLabel' => date('H:i', strtotime($c->start_time)),
        'timeDetail' => date('H:i', strtotime($c->start_time)) . ($c->end_time ? ' - '.date('H:i', strtotime($c->end_time)) : ''),
        'location' => $c->location, 'color' => $c->color, 'is_recurring' => true
    ]);
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
            <button onclick="window.setSchFilter('task', this)">Cá nhân</button>
            <button onclick="window.setSchFilter('event', this)">Sự kiện</button>
        </div>
    </div>

    <div class="date-slider" id="ui-date-slider"></div>
    <div id="dynamic-timeline"></div>

    <div class="quick-add-card" id="quick-add-section">
        <div id="form-title-text" style="font-weight: 700; margin-bottom: 15px; text-transform: uppercase;">TẠO LỊCH MỚI</div>
        
        <div id="form-task-fields" style="display: none; flex-direction: column; gap: 10px;">
            <div style="display: flex; gap: 10px;">
                <input type="text" id="new-task-title" placeholder="Tiêu đề..." style="flex: 2; padding: 10px; border-radius: 8px; border: 1px solid #ddd;">
                <input type="datetime-local" id="new-task-date" style="flex: 1; padding: 10px; border-radius: 8px; border: 1px solid #ddd;">
            </div>
            <textarea id="new-task-desc" placeholder="Mô tả chi tiết..." style="padding: 10px; border-radius: 8px; border: 1px solid #ddd; height: 60px; font-family: inherit;"></textarea>
            
            <div style="display: flex; gap: 10px; align-items: center;">
                <select id="new-task-comp-type" style="flex: 1; padding: 12px; border-radius: 8px; border: 1px solid #ddd; outline: none;">
                    <option value="simple">Chỉ cần nhấn "Xong"</option>
                    <option value="proof">Bắt buộc nộp Ảnh & Định vị GPS</option>
                </select>
                <button onclick="window.handleSaveTask(this)" style="background: #1877f2; color: #fff; border: none; padding: 12px 25px; border-radius: 8px; font-weight: 600; cursor: pointer;">LƯU LỊCH TRÌNH</button>
            </div>
        </div>

        <div id="form-class-fields" style="display: none; flex-direction: column; gap: 10px;">
            <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                <input type="text" id="new-class-title" placeholder="Tên môn học..." style="flex: 2; min-width: 200px; padding: 10px; border-radius: 8px; border: 1px solid #ddd;">
                <select id="new-class-day" style="flex: 1; min-width: 120px; padding: 10px; border-radius: 8px; border: 1px solid #ddd; outline: none;">
                    <option value="2">Thứ 2</option><option value="3">Thứ 3</option><option value="4">Thứ 4</option>
                    <option value="5">Thứ 5</option><option value="6">Thứ 6</option><option value="7">Thứ 7</option>
                    <option value="1">Chủ nhật</option>
                </select>
            </div>
            <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                <input type="time" id="new-class-start" style="flex: 1; padding: 10px; border-radius: 8px; border: 1px solid #ddd;" title="Giờ bắt đầu">
                <input type="time" id="new-class-end" style="flex: 1; padding: 10px; border-radius: 8px; border: 1px solid #ddd;" title="Giờ kết thúc">
                <input type="text" id="new-class-location" placeholder="Phòng học (VD: P.402-D2)" style="flex: 1; padding: 10px; border-radius: 8px; border: 1px solid #ddd;">
            </div>
            <button onclick="window.handleSaveClass(this)" style="background: #23a559; color: #fff; border: none; padding: 12px 25px; border-radius: 8px; font-weight: 600; cursor: pointer; align-self: flex-end;">THÊM VÀO THỜI KHÓA BIỂU</button>
        </div>
    </div>
</div>

<div class="proof-modal-overlay" id="proof-history-modal">
    <div class="proof-modal-content">
        <h3 style="margin-top: 0; color: #1c1e21; font-size: 18px; border-bottom: 1px solid #eee; padding-bottom: 10px;">Lịch sử nộp minh chứng</h3>
        <div id="proof-history-list"></div>
        <button onclick="document.getElementById('proof-history-modal').style.display='none'" style="width: 100%; margin-top: 15px; padding: 10px; border-radius: 8px; border: 1px solid #ddd; background: #fff; font-weight: 600; cursor: pointer;">Đóng lại</button>
    </div>
</div>

<input type="file" id="static-proof-upload" accept="image/*" style="display: none;">

<script>
    (function() {
        window.schData = {!! $events->concat($tasks)->toJson() !!};
        window.recurringClasses = {!! $recurring->toJson() !!};
        
        const now = new Date();
        const state = { month: now.getMonth(), year: now.getFullYear(), selectedDate: now.toLocaleDateString('en-CA'), filter: 'all' };

        window.updateScheduleUI = function() {
            state.month = parseInt(document.getElementById('sel-month').value);
            state.year = parseInt(document.getElementById('sel-year').value);
            state.selectedDate = new Date(state.year, state.month, 1).toLocaleDateString('en-CA');
            renderSlider(); renderTimeline();
        };

        window.setSchFilter = function(f, btn) {
            state.filter = f;
            if (btn) {
                document.querySelectorAll('.pill-filters button').forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
            }
            
            const addSection = document.getElementById('quick-add-section');
            const titleText = document.getElementById('form-title-text');
            const formTask = document.getElementById('form-task-fields');
            const formClass = document.getElementById('form-class-fields');

            if (f === 'task') {
                addSection.style.display = 'block';
                formTask.style.display = 'flex'; formClass.style.display = 'none';
                titleText.innerHTML = '<i class="fa-solid fa-user"></i> TẠO LỊCH CÁ NHÂN MỚI';
            } else if (f === 'class') {
                addSection.style.display = 'block';
                formTask.style.display = 'none'; formClass.style.display = 'flex';
                titleText.innerHTML = '<i class="fa-solid fa-graduation-cap"></i> THÊM MÔN HỌC VÀO THỜI KHÓA BIỂU';
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

        window.renderTimeline = function() {
            const container = document.getElementById('dynamic-timeline');
            const d = new Date(state.selectedDate);
            const dbDay = d.getDay() === 0 ? 1 : d.getDay() + 1;
            
            const dayClasses = window.recurringClasses.filter(c => c.day_of_week === dbDay);
            let filtered = [];

            if (state.filter === 'all') filtered = window.schData.filter(i => i.start === state.selectedDate).concat(dayClasses);
            else if (state.filter === 'class') filtered = dayClasses;
            else filtered = window.schData.filter(i => i.start === state.selectedDate && i.type === state.filter);

            filtered.sort((a, b) => a.timeLabel.localeCompare(b.timeLabel));

            if(filtered.length === 0) {
                container.innerHTML = `<div style="text-align:center; padding:40px; color:#8e8e8e; border:1px dashed #ddd; border-radius:12px;">Trống</div>`;
                return;
            }

            container.innerHTML = filtered.map(item => {
                let actionBtn = '', viewHistoryBtn = '', deleteBtn = '';
                
                if (item.type === 'task') {
                    if (item.completion_type === 'proof') {
                        actionBtn = `<button class="btn-action-small btn-proof" onclick="window.openProofUpload(${item.id})"><i class="fa-solid fa-camera"></i> Nộp Ảnh + Vị trí</button>`;
                    } else {
                        actionBtn = `<button class="btn-done" onclick="window.toggleTaskStatus(${item.id})">${item.is_done ? 'Hoàn tác' : 'Xong'}</button>`;
                    }
                    deleteBtn = `<button class="btn-action-small btn-delete" onclick="window.deleteScheduleItem(${item.id})"><i class="fa-solid fa-trash"></i> Xóa</button>`;
                } else if (item.is_recurring) {
                    deleteBtn = `<button class="btn-action-small btn-delete" onclick="window.deleteClassItem(${item.id})"><i class="fa-solid fa-trash"></i> Xóa môn học</button>`;
                }

                if (item.proofs && item.proofs.length > 0) {
                    const encodedData = encodeURIComponent(JSON.stringify(item.proofs));
                    viewHistoryBtn = `<button class="btn-action-small" style="color:#23a559; border-color:#23a559;" onclick="window.viewProofHistory('${encodedData}')"><i class="fa-solid fa-clock-rotate-left"></i> Xem bản nộp (v${item.proofs[0].version})</button>`;
                }

                return `
                <div class="t-row" id="t-row-${item.id || 'rec'}">
                    <div class="t-time-col">${item.timeLabel}</div>
                    <div class="t-card ${item.is_done ? 'completed' : ''}" style="border-left-color: ${item.color}">
                        <div class="t-header">
                            <div class="t-info">
                                <div class="title">${item.title}</div>
                                <div class="time">${item.timeDetail ? item.timeDetail + ' | ' : ''}${item.location || 'Chưa cập nhật'}</div>
                            </div>
                            ${actionBtn}
                        </div>
                        ${item.desc ? `<div class="t-desc">${item.desc}</div>` : ''}
                        <div class="t-footer-actions">
                            ${viewHistoryBtn}
                            ${deleteBtn}
                        </div>
                    </div>
                </div>`;
            }).join('');
        };

        window.deleteScheduleItem = async function(id) {
            if(!confirm("Bạn có chắc muốn xóa?")) return;
            try { await fetch(`/profile/${window.studentCode}/tasks/${id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } }); window.switchProfileTab('schedule'); } catch(e) {}
        };
        
        window.deleteClassItem = async function(id) {
            if(!confirm("Xóa môn này khỏi thời khóa biểu hàng tuần?")) return;
            try { await fetch(`/profile/${window.studentCode}/classes/${id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } }); window.switchProfileTab('schedule'); } catch(e) {}
        };

        window.toggleTaskStatus = async function(id) {
            try { await fetch(`/profile/${window.studentCode}/tasks/${id}/toggle`, { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } }); window.switchProfileTab('schedule'); } catch(e) {}
        };

        window.handleSaveTask = async function(btn) {
            const title = document.getElementById('new-task-title').value;
            const date = document.getElementById('new-task-date').value;
            const desc = document.getElementById('new-task-desc').value;
            const cType = document.getElementById('new-task-comp-type').value;

            if(!title || !date) return alert("Thiếu thông tin!");
            btn.disabled = true;
            try {
                await fetch(`/profile/${window.studentCode}/tasks`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ title, due_date: date, description: desc, completion_type: cType, type: 'task' })
                });
                window.switchProfileTab('schedule');
            } catch(e) { alert("Lỗi!"); btn.disabled = false; }
        };

        window.handleSaveClass = async function(btn) {
            const title = document.getElementById('new-class-title').value;
            const day = document.getElementById('new-class-day').value;
            const start = document.getElementById('new-class-start').value;
            const end = document.getElementById('new-class-end').value;
            const loc = document.getElementById('new-class-location').value;

            if(!title || !start) return alert("Vui lòng nhập tên môn và giờ bắt đầu!");
            btn.disabled = true;
            try {
                await fetch(`/profile/${window.studentCode}/classes`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ title, day_of_week: day, start_time: start, end_time: end, location: loc })
                });
                window.switchProfileTab('schedule');
            } catch(e) { alert("Lỗi!"); btn.disabled = false; }
        };

        // --- NGĂN CHẶN LỖI DOUBLE VÀ ÉP BUỘC CÓ GPS ---
        const fileInputEl = document.getElementById('static-proof-upload');
        window.uploadingTargetId = null;
        window.isUploadingProof = false; // Cờ khóa click

        window.openProofUpload = function(taskId) { 
            if (window.isUploadingProof) return alert("Đang xử lý, vui lòng chờ...");
            window.uploadingTargetId = taskId;
            fileInputEl.value = ''; 
            fileInputEl.click(); 
        };

        fileInputEl.onchange = function(e) {
            const file = e.target.files[0]; 
            if(!file || !window.uploadingTargetId) return;
            
            const taskId = window.uploadingTargetId; 
            window.uploadingTargetId = null; 
            window.isUploadingProof = true; // Khóa toàn bộ các nút nộp khác

            alert("Đang định vị... Bắt buộc phải có Vị Trí mới được nộp. Vui lòng chọn 'Cho phép' trên trình duyệt.");
            
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    (pos) => {
                        // Chỉ khi lấy được GPS mới gọi hàm sendProofData
                        sendProofData(file, taskId, pos.coords.latitude, pos.coords.longitude);
                    },
                    (err) => {
                        window.isUploadingProof = false;
                        fileInputEl.value = '';
                        
                        let msg = "Lỗi lấy vị trí: ";
                        if(err.code == 1) msg += "Bạn đã từ chối quyền truy cập vị trí.";
                        else if(err.code == 2) msg += "Không có tín hiệu mạng/GPS.";
                        else if(err.code == 3) msg += "Hết thời gian chờ.";
                        
                        // Báo lỗi và DỪNG LẠI, KHÔNG GỌI sendProofData
                        alert(msg + " \n\n=> Hủy nộp minh chứng vì KHÔNG CÓ GPS!");
                    },
                    { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 } 
                );
            } else { 
                window.isUploadingProof = false;
                fileInputEl.value = '';
                alert("Trình duyệt không hỗ trợ GPS. Hủy nộp.");
            }
        };

        async function sendProofData(file, taskId, lat, lng) {
            const formData = new FormData(); 
            formData.append('proof', file);
            formData.append('latitude', lat); 
            formData.append('longitude', lng);
            
            try {
                const res = await fetch(`/profile/${window.studentCode}/tasks/${taskId}/proof-gps`, { 
                    method: 'POST', 
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }, 
                    body: formData 
                });
                if(res.ok) {
                    alert("Đã nộp minh chứng kèm Vị trí thành công!");
                    window.switchProfileTab('schedule');
                } else {
                    alert("Lỗi server khi lưu file.");
                }
            } catch(e) { 
                alert("Lỗi kết nối mạng!"); 
            } finally {
                window.isUploadingProof = false; // Mở khóa
                fileInputEl.value = '';
            }
        }

       window.viewProofHistory = function(encodedData) {
            const proofs = JSON.parse(decodeURIComponent(encodedData)); 
            let html = '';
            proofs.forEach(p => {
                const date = new Date(p.created_at).toLocaleString('vi-VN');
                
                // ĐOẠN NÀY ĐÃ ĐƯỢC SỬA LẠI CHUẨN XÁC 100%
                const locTag = p.latitude 
                    ? `<a href="https://www.google.com/maps?q=${p.latitude},${p.longitude}" target="_blank" style="color:#1877f2; text-decoration:none; font-weight:600;"><i class="fa-solid fa-location-dot"></i> Xem trên Bản đồ</a>` 
                    : '<span style="color:#8e8e8e;">Không có vị trí GPS</span>';
                
                html += `
                <div class="proof-version-item">
                    <div style="font-weight:700; color:#1c1e21; margin-bottom:5px;">Phiên bản ${p.version}</div>
                    <div style="font-size:12px; color:#65676b; margin-bottom:5px;">Nộp lúc: ${date}</div>
                    <div style="font-size:13px; margin-bottom:10px;">${locTag}</div>
                    <a href="${p.file_url}" target="_blank">
                        <img src="${p.file_url}" alt="Minh chứng v${p.version}" style="width:100%; border-radius:8px; border:1px solid #ddd; object-fit:cover;">
                    </a>
                </div>`;
            });
            document.getElementById('proof-history-list').innerHTML = html;
            document.getElementById('proof-history-modal').style.display = 'flex';
        };

        const mSel = document.getElementById('sel-month'); const ySel = document.getElementById('sel-year');
        const months = ["Tháng 1", "Tháng 2", "Tháng 3", "Tháng 4", "Tháng 5", "Tháng 6", "Tháng 7", "Tháng 8", "Tháng 9", "Tháng 10", "Tháng 11", "Tháng 12"];
        mSel.innerHTML = months.map((m, i) => `<option value="${i}" ${i === state.month ? 'selected' : ''}>${m}</option>`).join('');
        ySel.innerHTML = [state.year-1, state.year, state.year+1].map(y => `<option value="${y}" ${y === state.year ? 'selected' : ''}>Năm ${y}</option>`).join('');

        renderSlider(); 
        const defaultBtn = document.querySelector('.pill-filters button.active');
        window.setSchFilter('all', defaultBtn);
    })();
</script>