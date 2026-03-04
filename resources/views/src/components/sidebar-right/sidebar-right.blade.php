<link rel="stylesheet" href="{{ asset('views/src/components/sidebar-right/sidebar-right.css') }}">

<div class="tools-container">
    <div class="floating-icon-bar" id="quick-links-list">
        </div>
</div>

<div class="modal-overlay" id="add-link-modal">
    <div class="modal-content">
        <div class="modal-header" id="modal-title">Thêm lối tắt mới</div>
        <input type="text" id="link-name-input" class="modal-input" placeholder="Tên gợi nhớ (VD: Facebook)">
        <input type="url" id="link-url-input" class="modal-input" placeholder="Đường dẫn (VD: facebook.com)">
        <div class="modal-actions">
            <button class="btn-cancel" onclick="closeModal()">Hủy</button>
            <button class="btn-save" onclick="saveLink()">Lưu</button>
        </div>
    </div>
</div>

<script>
    (function initQuickLinks() {
        // FIX LỖI POPUP BỊ KẸT TRONG CỘT MOBILE:
        const linkModal = document.getElementById('add-link-modal');
        if (linkModal && linkModal.parentElement !== document.body) {
            document.body.appendChild(linkModal);
        }

        const defaultLinks = [
            { name: "GitHub Nhóm 6", url: "https://github.com", iconClass: "fa-brands fa-github" },
            { name: "LinkedIn", url: "https://linkedin.com", iconClass: "fa-brands fa-linkedin-in" },
            { name: "Instagram", url: "https://instagram.com", iconClass: "fa-brands fa-instagram" }
        ];

        let editingIndex = -1;

        function getUserStorageKey() {
            const userStr = sessionStorage.getItem('currentUser');
            return userStr ? 'quickLinks_' + JSON.parse(userStr).msv : 'quickLinks_guest';
        }

        function getFavicon(url) {
            try {
                const domain = new URL(url).hostname;
                return `<img src="https://www.google.com/s2/favicons?domain=${domain}&sz=64" class="custom-favicon">`;
            } catch (e) {
                return '<i class="fa-solid fa-link"></i>';
            }
        }

        window.renderQuickLinks = function() {
            const list = document.getElementById('quick-links-list');
            if (!list) return;

            const storageKey = getUserStorageKey();
            const links = JSON.parse(localStorage.getItem(storageKey)) || defaultLinks;
            
            list.innerHTML = '';
            links.forEach((link, index) => {
                const displayIcon = link.iconClass ? `<i class="${link.iconClass}"></i>` : getFavicon(link.url);
                list.innerHTML += `
                    <div class="quick-link-wrapper">
                        <a href="${link.url}" target="_blank" class="quick-link-item" title="${link.name}">
                            ${displayIcon}
                        </a>
                        <div class="delete-link" title="Xóa" onclick="deleteLink(event, ${index})"><i class="fa-solid fa-xmark"></i></div>
                        <div class="edit-link" title="Sửa" onclick="openEditModal(event, ${index})"><i class="fa-solid fa-pen"></i></div>
                    </div>
                `;
            });

            list.innerHTML += `
                <div class="quick-link-wrapper">
                    <div class="add-link-box" onclick="openAddModal()" title="Thêm lối tắt mới"><i class="fa-solid fa-plus"></i></div>
                </div>
            `;
        };

        window.openAddModal = function() {
            editingIndex = -1;
            document.getElementById('modal-title').innerText = "Thêm lối tắt mới";
            document.getElementById('link-name-input').value = '';
            document.getElementById('link-url-input').value = '';
            document.getElementById('add-link-modal').classList.add('active');
        };

        window.openEditModal = function(event, index) {
            event.preventDefault(); event.stopPropagation();
            editingIndex = index;
            const storageKey = getUserStorageKey();
            const linkToEdit = (JSON.parse(localStorage.getItem(storageKey)) || defaultLinks)[index];
            document.getElementById('modal-title').innerText = "Sửa lối tắt";
            document.getElementById('link-name-input').value = linkToEdit.name;
            document.getElementById('link-url-input').value = linkToEdit.url;
            document.getElementById('add-link-modal').classList.add('active');
        };

        window.closeModal = function() { document.getElementById('add-link-modal').classList.remove('active'); };

        window.saveLink = function() {
            const name = document.getElementById('link-name-input').value.trim();
            let url = document.getElementById('link-url-input').value.trim();
            if (!name || !url) return alert("Sếp điền đủ thông tin nhé!");
            if (!url.startsWith('http://') && !url.startsWith('https://')) url = 'https://' + url;

            const storageKey = getUserStorageKey();
            const links = JSON.parse(localStorage.getItem(storageKey)) || defaultLinks;

            if (editingIndex > -1) {
                links[editingIndex].name = name;
                links[editingIndex].url = url;
                delete links[editingIndex].iconClass; 
            } else {
                links.push({ name: name, url: url });
            }

            localStorage.setItem(storageKey, JSON.stringify(links));
            renderQuickLinks();
            closeModal();
        };

        window.deleteLink = function(event, index) {
            event.preventDefault(); event.stopPropagation();
            if(confirm("Xóa nhé sếp?")) {
                const storageKey = getUserStorageKey();
                const links = JSON.parse(localStorage.getItem(storageKey)) || defaultLinks;
                links.splice(index, 1);
                localStorage.setItem(storageKey, JSON.stringify(links));
                renderQuickLinks();
            }
        };

        renderQuickLinks();
    })();
</script>