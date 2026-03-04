<link rel="stylesheet" href="{{ asset('views/src/components/sidebar-left/sidebar-left.css') }}">
<div class="sidebar-left-wrapper">
    <div class="page-server-bar" id="left-server-bar">
    </div>
    
    <div class="context-menu-panel">
        <div class="bav-logo-placeholder">
            <img src="https://upload.wikimedia.org/wikipedia/vi/1/1b/H%E1%BB%8Dc_vi%E1%BB%87n_Ng%C3%A2n_h%C3%A0ng_logo.png">
        </div>
        <div class="card-box">
            <h4>Yêu thích</h4>
            <div class="sk-line" style="margin-top: 10px; width: 100%; height: 12px; background: #e5e7eb; border-radius: 6px;"></div>
        </div>
    </div>
</div>

<div class="modal-overlay" id="group-modal">
    <div class="modal-content">
        <div class="modal-header" id="group-modal-title">Tạo Folder mới</div>
        <input type="text" id="group-name-input" class="modal-input" placeholder="Tên Folder">
        <input type="color" id="group-color-input" class="modal-input" style="height: 50px; padding: 5px;" value="#23a559">
        <div class="modal-actions">
            <button class="btn-cancel" onclick="closeGroupModal()">Hủy</button>
            <button class="btn-save" onclick="saveGroup()">Lưu</button>
        </div>
    </div>
</div>

<script>
    (function initLeftSidebar() {
        const groupModal = document.getElementById('group-modal');
        if (groupModal && groupModal.parentElement !== document.body) {
            document.body.appendChild(groupModal);
        }

        const defaultState = [
            { id: 'home', type: 'page', moduleType: 'feed', moduleFolder: 'profile', name: 'Hồ sơ của tôi', isStatic: true, icon: '<i class="fa-solid fa-house-chimney-user"></i>' },
            { id: 'sep1', type: 'separator' },
            { id: 'g_itde', type: 'group', name: 'Khoa CNTT và KTS', color: '#5865f2', expanded: true },
            { id: 'p_itde', type: 'page', moduleType: 'feed', moduleFolder: 'page', name: 'Khoa CNTT', groupId: 'g_itde', img: 'https://ui-avatars.com/api/?name=IT&background=5865f2&color=fff' },
            { id: 'p_bit', type: 'page', moduleType: 'feed', moduleFolder: 'page', name: 'CLB BIT', groupId: 'g_itde', img: 'https://ui-avatars.com/api/?name=BI&background=5865f2&color=fff' },
            { id: 'p_bit_nb', type: 'page', moduleType: 'feed', moduleFolder: 'page', name: 'Nội bộ BIT', groupId: 'g_itde', img: 'https://ui-avatars.com/api/?name=NB&background=1e1f22&color=fff' },
            { id: 'g_hvnh', type: 'group', name: 'Học viện Ngân hàng', color: '#ed4245', expanded: false },
            { id: 'p_hvnh', type: 'page', moduleType: 'feed', moduleFolder: 'page', name: 'HVNH', groupId: 'g_hvnh', img: 'https://ui-avatars.com/api/?name=HV&background=ed4245&color=fff' },
            { id: 'p_hsv', type: 'page', moduleType: 'feed', moduleFolder: 'page', name: 'Hội sinh viên', groupId: 'g_hvnh', img: 'https://ui-avatars.com/api/?name=HS&background=ed4245&color=fff' },
            
            // --- THÊM 10 CÁI THEO YÊU CẦU CỦA SẾP ---
            { id: 'sep2', type: 'separator' },
            { id: 'g_test', type: 'group', name: 'Nhóm Test Cuộn', color: '#f1c40f', expanded: true },
            { id: 'p_test1', type: 'page', moduleType: 'feed', moduleFolder: 'page', name: 'Page Test 1', groupId: 'g_test', img: 'https://ui-avatars.com/api/?name=T1&background=f1c40f&color=fff' },
            { id: 'p_test2', type: 'page', moduleType: 'feed', moduleFolder: 'page', name: 'Page Test 2', groupId: 'g_test', img: 'https://ui-avatars.com/api/?name=T2&background=f1c40f&color=fff' },
            { id: 'p_test3', type: 'page', moduleType: 'feed', moduleFolder: 'page', name: 'Page Test 3', groupId: 'g_test', img: 'https://ui-avatars.com/api/?name=T3&background=f1c40f&color=fff' },
            { id: 'p_test4', type: 'page', moduleType: 'feed', moduleFolder: 'page', name: 'Page Test 4', groupId: 'g_test', img: 'https://ui-avatars.com/api/?name=T4&background=f1c40f&color=fff' },
            { id: 'p_test5', type: 'page', moduleType: 'feed', moduleFolder: 'page', name: 'Page Test 5', groupId: 'g_test', img: 'https://ui-avatars.com/api/?name=T5&background=f1c40f&color=fff' },
            { id: 'p_test6', type: 'page', moduleType: 'feed', moduleFolder: 'page', name: 'Page Test 6', groupId: 'g_test', img: 'https://ui-avatars.com/api/?name=T6&background=f1c40f&color=fff' },
            { id: 'p_test7', type: 'page', moduleType: 'feed', moduleFolder: 'page', name: 'Page Test 7', groupId: 'g_test', img: 'https://ui-avatars.com/api/?name=T7&background=f1c40f&color=fff' },
            { id: 'p_test8', type: 'page', moduleType: 'feed', moduleFolder: 'page', name: 'Page Test 8', groupId: 'g_test', img: 'https://ui-avatars.com/api/?name=T8&background=f1c40f&color=fff' },
            { id: 'p_test9', type: 'page', moduleType: 'feed', moduleFolder: 'page', name: 'Page Test 9', groupId: 'g_test', img: 'https://ui-avatars.com/api/?name=T9&background=f1c40f&color=fff' },
            { id: 'p_test10', type: 'page', moduleType: 'feed', moduleFolder: 'page', name: 'Page Test 10', groupId: 'g_test', img: 'https://ui-avatars.com/api/?name=T10&background=f1c40f&color=fff' }
        ];

        // SỬA LẠI: Key v24 - Xóa sạch cache cũ để nhận 10 trang mới này
        function getUserStorageKey() {
            const userStr = sessionStorage.getItem('currentUser');
            return userStr ? 'leftSidebar_v24_' + JSON.parse(userStr).msv : 'leftSidebar_v24_guest';
        }

        const storageKey = getUserStorageKey();

        // Dọn dẹp cache rác cũ
        Object.keys(localStorage).forEach(key => {
            if(key.startsWith('leftSidebar_') && key !== storageKey) {
                localStorage.removeItem(key);
            }
        });

        let state = JSON.parse(localStorage.getItem(storageKey)) || defaultState;
        
        // Cài đặt mặc định ban đầu không có page nào được chọn
        if (typeof window.currentSelectedPageId === 'undefined') {
            window.currentSelectedPageId = null; 
        }
        window.draggedId = null;

        function normalizeState() {
            const newState = [];
            const topLevel = state.filter(x => !x.groupId);
            topLevel.forEach(parent => {
                newState.push(parent);
                if (parent.type === 'group') {
                    const children = state.filter(x => x.groupId === parent.id);
                    newState.push(...children);
                }
            });
            state = newState;
        }

        window.renderLeftBar = function() {
            const container = document.getElementById('left-server-bar');
            if (!container) return;
            
            normalizeState();
            container.innerHTML = '';

            const topLevelItems = state.filter(x => !x.groupId);
            
            topLevelItems.forEach((item) => {
                if (item.type === 'page') {
                    container.innerHTML += renderPageIcon(item);
                } else if (item.type === 'separator') {
                    container.innerHTML += `<div class="page-separator"></div>`;
                } else if (item.type === 'group') {
                    const children = state.filter(p => p.type === 'page' && p.groupId === item.id);
                    const dragEvents = `ondragover="handleDragOver(event, '${item.id}', true)" ondragleave="handleDragLeave(event)" ondrop="handleDrop(event, '${item.id}')" draggable="true" ondragstart="dragStart(event, '${item.id}')"`;
                    
                    if (item.expanded) {
                        let childrenHtml = children.map(child => renderPageIcon(child, item.color)).join('');
                        container.innerHTML += `
                            <div class="group-expanded-pill" style="background-color: ${item.color}33;" id="wrap_${item.id}"> 
                                <div class="group-wrapper" ${dragEvents}>
                                    <div class="page-icon" style="background-color: ${item.color}; border-radius: 16px;" 
                                         onclick="toggleGroup('${item.id}')" title="${item.name}">
                                        <i class="fa-regular fa-folder-open"></i>
                                    </div>
                                    <div class="group-delete" onclick="deleteGroup(event, '${item.id}')"><i class="fa-solid fa-xmark"></i></div>
                                    <div class="group-edit" onclick="openGroupModal(event, '${item.id}')"><i class="fa-solid fa-pen"></i></div>
                                </div>
                                <div class="group-items-container">${childrenHtml}</div>
                            </div>`;
                    } else {
                        let miniIcons = children.slice(0, 4).map(child => child.img ? `<img src="${child.img}">` : `<div class="mini-fallback">${child.icon}</div>`).join('');
                        container.innerHTML += `
                            <div class="group-wrapper" id="wrap_${item.id}" ${dragEvents}>
                                <div class="page-icon folder-collapsed-grid" style="background-color: ${item.color}80;" 
                                     onclick="toggleGroup('${item.id}')" title="${item.name}">${miniIcons}</div>
                                <div class="group-delete" onclick="deleteGroup(event, '${item.id}')"><i class="fa-solid fa-xmark"></i></div>
                                <div class="group-edit" onclick="openGroupModal(event, '${item.id}')"><i class="fa-solid fa-pen"></i></div>
                            </div>`;
                    }
                }
            });

            container.innerHTML += `
                <div class="add-group-btn" title="Tạo Folder" onclick="openGroupModal(event, null)"><i class="fa-solid fa-plus"></i></div>
                <div class="explore-btn" title="Khám phá"><i class="fa-solid fa-compass"></i></div>
            `;
        };

        function renderPageIcon(page, folderColor = null) {
            const content = page.img ? `<img src="${page.img}">` : page.icon;
            const style = folderColor ? `style="background-color: ${folderColor}33;"` : '';
            
            const dataAttrs = `data-type="${page.moduleType || 'feed'}" data-module="${page.moduleFolder || 'page'}"`;

            // Kiểm tra biến toàn cục xem nút này có đang thực sự được bấm không, nếu có thì thêm class 'active'
            const isActive = window.currentSelectedPageId === page.id ? 'active' : '';

            if (page.isStatic) {
                 return `<div class="page-icon ${isActive}" data-id="${page.id}" ${dataAttrs} onclick="navigateToPage('${page.id}')" title="${page.name}">${content}</div>`;
            }

            return `<div class="page-icon ${isActive}" data-id="${page.id}" ${style} title="${page.name}"
                         ${dataAttrs}
                         onclick="navigateToPage('${page.id}')"
                         draggable="true" ondragstart="dragStart(event, '${page.id}')"
                         ondragover="handleDragOver(event, '${page.id}', false)" 
                         ondragleave="handleDragLeave(event)" 
                         ondrop="handleDrop(event, '${page.id}')">
                        ${content}
                    </div>`;
        }

        window.navigateToPage = (id) => {
            document.querySelectorAll('.page-icon').forEach(el => el.classList.remove('active'));
            const targetEl = document.querySelector(`.page-icon[data-id="${id}"]`);
            if (targetEl) targetEl.classList.add('active');

            window.currentSelectedPageId = id;

            const url = new URL(window.location);
            if (id === 'home') {
                url.searchParams.delete('pageId');
            } else {
                url.searchParams.set('pageId', id);
            }
            window.history.pushState({ pageId: id }, '', url);
        };

        window.dragStart = (e, id) => { window.draggedId = id; e.dataTransfer.effectAllowed = 'move'; e.stopPropagation(); };
        window.handleDragLeave = (e) => { e.currentTarget.classList.remove('drag-over-top', 'drag-over-bottom', 'drag-over-folder'); };
        window.handleDragOver = (e, targetId, isGroup) => {
            e.preventDefault(); e.stopPropagation();
            if (window.draggedId === targetId) return;
            const targetEl = e.currentTarget;
            targetEl.classList.remove('drag-over-top', 'drag-over-bottom', 'drag-over-folder');
            const rect = targetEl.getBoundingClientRect();
            const y = e.clientY - rect.top;
            if (isGroup && y > rect.height * 0.25 && y < rect.height * 0.75) targetEl.classList.add('drag-over-folder');
            else if (y < rect.height / 2) targetEl.classList.add('drag-over-top');
            else targetEl.classList.add('drag-over-bottom');
        };
        window.handleDrop = (e, targetId) => {
            e.preventDefault(); e.stopPropagation();
            const targetEl = e.currentTarget;
            const isTop = targetEl.classList.contains('drag-over-top');
            const isBottom = targetEl.classList.contains('drag-over-bottom');
            const isFolder = targetEl.classList.contains('drag-over-folder');
            targetEl.classList.remove('drag-over-top', 'drag-over-bottom', 'drag-over-folder');

            if (!window.draggedId || window.draggedId === targetId) return;

            const draggedIdx = state.findIndex(x => x.id === window.draggedId);
            const targetObj = state.find(x => x.id === targetId);
            if (draggedIdx === -1 || !targetObj || targetObj.isStatic) return;

            const draggedItem = state[draggedIdx];
            if (draggedItem.type === 'group' && isFolder) return;

            state.splice(draggedIdx, 1);
            let newTargetIdx = state.findIndex(x => x.id === targetId);

            if (isFolder && targetObj.type === 'group') {
                draggedItem.groupId = targetObj.id; 
                state.splice(newTargetIdx + 1, 0, draggedItem); 
            } else {
                if (draggedItem.type === 'group') {
                    draggedItem.groupId = null;
                    if (targetObj.groupId) {
                        newTargetIdx = state.findIndex(x => x.id === targetObj.groupId);
                        if (isBottom) {
                            while(newTargetIdx + 1 < state.length && state[newTargetIdx + 1].groupId === targetObj.groupId) newTargetIdx++;
                        }
                    }
                } else {
                    draggedItem.groupId = targetObj.groupId || null; 
                }
                if (isBottom) state.splice(newTargetIdx + 1, 0, draggedItem);
                else state.splice(newTargetIdx, 0, draggedItem);
            }
            window.draggedId = null;
            normalizeState(); saveAndRender();
        };

        window.toggleGroup = (id) => { const g = state.find(x => x.id === id); if(g){ g.expanded = !g.expanded; saveAndRender(); } };
        window.openGroupModal = (e, id) => { 
            if(e){ e.preventDefault(); e.stopPropagation(); }
            window.editingGroupId = id;
            if(id) {
                const g = state.find(x => x.id === id);
                document.getElementById('group-modal-title').innerText = "Sửa Folder";
                document.getElementById('group-name-input').value = g.name;
                document.getElementById('group-color-input').value = g.color;
            } else {
                document.getElementById('group-modal-title').innerText = "Tạo Folder mới";
                document.getElementById('group-name-input').value = '';
                document.getElementById('group-color-input').value = '#23a559';
            }
            document.getElementById('group-modal').classList.add('active');
        };
        window.closeGroupModal = () => document.getElementById('group-modal').classList.remove('active');
        window.saveGroup = () => {
            const n = document.getElementById('group-name-input').value.trim();
            const c = document.getElementById('group-color-input').value;
            if(!n) return;
            if(window.editingGroupId) {
                const g = state.find(x => x.id === window.editingGroupId);
                g.name = n; g.color = c;
            } else {
                state.push({ id: 'g_' + Date.now(), type: 'group', name: n, color: c, expanded: false });
            }
            normalizeState(); saveAndRender(); closeGroupModal();
        };
        window.deleteGroup = (e, id) => {
            e.preventDefault(); e.stopPropagation();
            if(confirm("Xóa nhé sếp?")){
                state.forEach(p => { if(p.groupId === id) p.groupId = null; });
                state = state.filter(x => x.id !== id);
                normalizeState(); saveAndRender();
            }
        };

        function saveAndRender() { localStorage.setItem(storageKey, JSON.stringify(state)); renderLeftBar(); }
        renderLeftBar();
    })();
</script>