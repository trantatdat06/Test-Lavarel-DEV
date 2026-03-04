<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hệ sinh thái Học tập AI - Nhóm 6 BAV</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('views/src/css/layout.css') }}">
    <link rel="stylesheet" href="{{ asset('views/src/css/modules-shared.css') }}">
    <style>
    /* 1. PC: LOGIC ẨN RIÊNG CỘT 3 WIDGET */
    body.widget-only-hidden .col-widget {
        width: 0 !important;
        border-left: none !important;
        opacity: 0 !important;
        overflow: hidden;
    }

    /* 2. FOOTER MENU: HIỆN TỪ IPAD TRỞ XUỐNG (< 1250px) */
    .mobile-bottom-nav {
        display: none !important; 
        position: fixed; bottom: 0; left: 0; width: 100%; height: 65px;
        background: #ffffff; border-top: 1px solid #dbdbdb;
        z-index: 2000; justify-content: space-around; align-items: center;
        box-shadow: 0 -2px 10px rgba(0,0,0,0.05);
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* -------------------------------------------------- */
    /* LOGIC RESPONSIVE IPAD & MOBILE (< 1250px) */
    /* -------------------------------------------------- */
    @media (max-width: 1250px) {
        .header-col-3, .col-widget { display: none !important; }
        .mobile-bottom-nav { display: flex !important; }

        .global-header {
            transition: transform 0.3s ease-in-out;
            position: fixed; top: 0; width: 100%; 
            z-index: 999999 !important;
        }

        .header-hidden { transform: translateY(-100%); }
        .footer-hidden { transform: translateY(100%); }

        .col-tools, .col-1 { 
            position: fixed !important; 
            z-index: 99999 !important; 
            background: #ffffff !important;
            display: flex !important; 
            visibility: visible !important;
            opacity: 1 !important;
            transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1) !important;
            
            /* TRỞ VỀ CODE CŨ: Canh từ dưới thanh header xuống đáy trên iPad */
            top: 65px; 
            height: calc(100vh - 130px); 
        }

        .col-tools { 
            right: 0 !important; 
            width: 60px !important; 
            transform: translateX(100%) !important; 
            border-left: 1px solid #f0f0f0 !important; 
            
            /* BO TRÒN GÓC DƯỚI BÊN TRÁI ĐỂ TRÔNG NHƯ PANEL NỔI */
            border-bottom-left-radius: 20px !important;
            box-shadow: -4px 4px 15px rgba(0,0,0,0.05);
        }
        
        .col-1 { 
            left: 0 !important; 
            width: 72px !important; 
            transform: translateX(-100%) !important; 
            
            /* BO TRÒN GÓC DƯỚI BÊN PHẢI ĐỂ TRÔNG NHƯ PANEL NỔI */
            border-bottom-right-radius: 20px !important;
            box-shadow: 4px 4px 15px rgba(0,0,0,0.05);
        }

        body:not(.sidebars-hidden) .col-tools { transform: translateX(0) !important; }
        body:not(.sidebars-hidden) .col-1 { transform: translateX(0) !important; }

        .main-body { 
            padding-top: 65px; padding-bottom: 65px; 
            display: block !important; width: 100vw; height: 100vh; overflow: hidden;
            transition: padding 0.3s ease;
        }

        .col-main-feed { 
            padding: 20px !important; width: 100% !important; max-width: 100vw;
            height: calc(100vh - 130px); 
            overflow-y: auto !important; -webkit-overflow-scrolling: touch;
            transition: height 0.3s ease;
        }

        body.bars-hidden .main-body { padding-top: 0; padding-bottom: 0; }
        body.bars-hidden .col-main-feed { height: 100vh; }
        body.bars-hidden .col-1, 
        body.bars-hidden .col-tools { top: 0; height: 100vh; border-radius: 0 !important; }

        .main-nav .icon-btn.active, .main-nav .icon-btn.active i,
        .widget-nav .icon-btn.active-blue, .widget-nav .icon-btn.active-blue i,
        .mobile-bottom-nav .icon-btn.active-blue, .mobile-bottom-nav .icon-btn.active-blue i,
        .mobile-widget-row .icon-btn.active-blue, .mobile-widget-row .icon-btn.active-blue i { color: #007bff !important; }

        .main-nav .icon-btn.active::after, .widget-nav .icon-btn.active-blue::after,
        .mobile-bottom-nav .icon-btn.active-blue::after, .mobile-widget-row .icon-btn.active-blue::after {
            content: ""; position: absolute; bottom: 0; left: 50%;
            transform: translateX(-50%); width: 60%; height: 3px;
            background-color: #007bff; border-radius: 3px 3px 0 0;
        }
    }

    /* -------------------------------------------------- */
    /* FIX iOS SAFARI: Scroll qua window để thanh trình duyệt tự thu nhỏ */
    /* -------------------------------------------------- */
    @media (max-width: 720px) {
        html {
            height: -webkit-fill-available;
        }
        .main-body {
            height: auto !important;
            overflow: visible !important;
            min-height: 100svh;
        }
        .col-main-feed {
            height: auto !important;
            min-height: 50vh;
            overflow-y: visible !important;
            overflow: visible !important;
            padding-bottom: 40px !important;
        }
        body {
            overflow-y: auto !important;
            height: 100%;
        }
    }

    /* -------------------------------------------------- */
    /* RIÊNG CHO ĐIỆN THOẠI (< 720px) */
    /* -------------------------------------------------- */
    @media (max-width: 720px) {
        #mobile-sidebar-toggle, .icon-btn { cursor: pointer !important; touch-action: manipulation; }
        .mobile-bottom-nav { display: none !important; }
        .global-header { height: 105px !important; }

        .header-inner {
            display: grid !important;
            grid-template-columns: 1fr auto !important;
            grid-template-rows: 55px 44px !important;
            padding: 0 12px !important;
            align-items: center !important;
        }

        .header-col-1, .header-col-3 { display: none !important; }

        .col-1, .col-tools { 
            top: 99px !important; 
            height: calc(100vh - 99px) !important; 
        }

        .header-col-2 {
            grid-column: 1 !important; grid-row: 1 / 3 !important;
            flex-direction: row !important; flex-wrap: wrap !important;
            align-content: flex-start !important; position: relative !important;
            overflow: hidden !important; padding: 0 !important; gap: 0 !important; height: 99px !important;
        }

        .header-col-4 {
            grid-column: 2 !important; grid-row: 1 !important;
            border: none !important; width: auto !important; height: 55px !important;
            align-items: center !important; position: absolute !important; right: 20px !important; top: 0px !important;
        }

        .logo-area {
            flex-shrink: 0 !important; font-size: 22px !important; white-space: nowrap !important;
            height: 55px !important; line-height: 55px !important; margin: 0 6px 0 10px !important;
        }

        .center-content {
            flex: 1 !important; display: flex !important; align-items: center !important;
            justify-content: flex-end !important; height: 55px !important; min-width: 0 !important; gap: 10px !important; padding: 0 !important;
        }

        .header-search {
            width: 36px !important; height: 36px !important; padding: 0 !important;
            justify-content: center !important; border-radius: 50% !important; background: #f0f2f5 !important;
            flex-shrink: 0 !important; margin-left: 0 !important; position: relative; right: 50px !important;
        }
        .header-search input { display: none !important; }

        .main-nav, .mobile-widget-row {
            position: absolute !important; bottom: 0 !important; left: 0 !important; right: 0 !important; height: 44px !important;
            justify-content: space-around !important; align-items: center !important; border-top: 1px solid #f0f0f0 !important;
            background: #fff !important; padding: 0 4px !important; margin: 0 !important; z-index: 1 !important;
        }

        .main-nav { display: flex !important; }
        .mobile-widget-row { display: none; }

        .main-nav .icon-btn, .mobile-widget-row .icon-btn { flex: 1 !important; height: 44px !important; font-size: 19px !important; border-radius: 4px !important; }

        #mobile-sidebar-toggle { order: -2 !important; }
        #mobile-widget-toggle  { order: 99 !important; }
        .mob-back-btn { color: #e74c3c !important; }

        .main-body { padding-top: 99px !important; padding-bottom: 0 !important; }
        .col-main-feed { height: calc(100vh - 99px) !important; }

        body.bars-hidden .main-body { padding-top: 0 !important; }
        body.bars-hidden .col-main-feed { height: 100vh !important; }
        body.bars-hidden .col-1, body.bars-hidden .col-tools { top: 0 !important; height: 100vh !important; border-radius: 0 !important; }
    }
</style>
</head>
<body>

    <div id="app-root" class="app-shell">
        <header id="header-placeholder" class="global-header"></header>
        <div class="main-body">
            <aside id="sidebar-left-placeholder" class="col-1"></aside>
            <main id="page-content-placeholder" class="col-main-feed animate-fade-in-up"></main>
            <aside id="sidebar-right-widget-placeholder" class="col-widget animate-fade-in-up"></aside>
            <aside id="sidebar-right-tools" class="col-tools"></aside>
        </div>

        <div id="mobile-footer" class="mobile-bottom-nav action-group widget-nav">
            <div class="icon-btn" data-type="widgets" data-module="notifications" title="Thông báo"><i class="fa-solid fa-bell"></i></div>
            <div class="icon-btn" data-type="widgets" data-module="todo" title="Công việc"><i class="fa-solid fa-clipboard-check"></i></div>
            <div class="icon-btn" data-type="widgets" data-module="calendar" title="Lịch học"><i class="fa-solid fa-calendar-days"></i></div>
            <div class="icon-btn" data-type="widgets" data-module="events" title="Sự kiện"><i class="fa-solid fa-calendar-check"></i></div>
            <div class="icon-btn" data-type="widgets" data-module="settings" title="Cài đặt"><i class="fa-solid fa-shapes"></i></div>
        </div>
    </div>

    <script>
    window.currentActiveWidget = 'notifications'; 
    let currentRotation = 0; 

    function executeDynamicScripts(element) {
        const scripts = element.querySelectorAll('script');
        scripts.forEach(oldScript => {
            const newScript = document.createElement('script');
            Array.from(oldScript.attributes).forEach(attr => newScript.setAttribute(attr.name, attr.value));
            newScript.appendChild(document.createTextNode(oldScript.innerHTML));
            oldScript.parentNode.replaceChild(newScript, oldScript);
        });
    }

    async function loadStatic(id, path) {
        try {
            const res = await fetch(path + '?v=' + new Date().getTime());
            const el = document.getElementById(id);
            if (el) { el.innerHTML = await res.text(); executeDynamicScripts(el); if (id === 'header-placeholder') initHeaderLogic(); }
        } catch (err) { console.error(`❌ Lỗi load ${path}:`, err); }
    }

    async function loadModule(placeholderId, type, folder) {
        const baseUrl = "{{ url('/') }}";
        let path = folder === 'profile' ? `${baseUrl}/src/modules/feed/profile/profile.blade.php` :
                   (type === 'feed' && ['home', 'favorite', 'explore', 'mixed'].includes(folder)) ? `${baseUrl}/src/modules/feed/feed-controller/feed-controller.blade.php` :
                   `${baseUrl}/src/modules/${type}/${folder}/${folder.split('/').pop()}.blade.php`;

        try {
            const res = await fetch(path + '?v=' + new Date().getTime());
            if (!res.ok) {
                alert(`[DEBUG LỖI] Không tìm thấy file tại đường dẫn: \n${path}`);
                throw new Error(`404 - Lỗi đường dẫn: ${path}`);
            }
            const placeholder = document.getElementById(placeholderId);
            if (!placeholder) return;
            
            placeholder.classList.remove('animate-fade-in-up');
            void placeholder.offsetWidth; 
            placeholder.innerHTML = await res.text();
            executeDynamicScripts(placeholder); 
            placeholder.classList.add('animate-fade-in-up');
            
            if (type === 'feed' && !folder.includes('profile')) renderFeedData(folder.split('/').pop());
        } catch (err) { console.error(err.message); }
    }

    function rotateToggleIcons() {
        currentRotation += 180;
        ['sidebar-toggle', 'mobile-sidebar-toggle'].forEach(id => {
            const icon = document.getElementById(id)?.querySelector('i');
            if (icon) {
                icon.style.transition = "transform 0.4s cubic-bezier(0.4, 0, 0.2, 1)";
                icon.style.transform = `rotate(${currentRotation}deg)`;
            }
        });
    }

    function initHeaderLogic() {
        const header = document.getElementById('header-placeholder');
        const footer = document.getElementById('mobile-footer');
        const scrollContainer = document.querySelector('.col-main-feed');
        let lastScrollTop = 0;

        // BẮT ĐẦU ĐOẠN JS ĐÃ SỬA LỖI RESIZE LIỆT NÚT
        let lastWidth = window.innerWidth;
        window.addEventListener('resize', () => {
            const currentWidth = window.innerWidth;

            // 1. Phục hồi thanh menu ngang khi phóng to từ Mobile lên PC/iPad
            if (lastWidth <= 720 && currentWidth > 720) {
                if (document.querySelector('.mobile-widget-row')) document.querySelector('.mobile-widget-row').style.display = '';
                if (document.querySelector('.main-nav')) document.querySelector('.main-nav').style.display = '';
            }

            // 2. Tự động ẩn sidebars khi thu nhỏ màn hình xuống < 1250px để tránh đè nút
            if (lastWidth > 1250 && currentWidth <= 1250) {
                if (!document.body.classList.contains('sidebars-hidden')) {
                    document.body.classList.add('sidebars-hidden');
                    rotateToggleIcons();
                }
            } else if (lastWidth <= 1250 && currentWidth > 1250) {
                if (document.body.classList.contains('sidebars-hidden')) {
                    document.body.classList.remove('sidebars-hidden');
                    rotateToggleIcons();
                }
            }

            lastWidth = currentWidth;
        });
        // KẾT THÚC ĐOẠN JS ĐÃ SỬA

        function handleScroll(scrollTop) {
            if (window.innerWidth >= 1250) return;

            if (window.innerWidth >= 720) {
                const hide = scrollTop > lastScrollTop && scrollTop > 50;
                header.classList.toggle('header-hidden', hide);
                footer.classList.toggle('footer-hidden', hide);
                document.body.classList.toggle('bars-hidden', hide);
            } else {
                if (Math.abs(scrollTop - lastScrollTop) > 2 && !document.body.classList.contains('sidebars-hidden')) {
                    document.body.classList.add('sidebars-hidden');
                    rotateToggleIcons();
                }
            }
            lastScrollTop = Math.max(0, scrollTop);
        }

        if (scrollContainer) {
            scrollContainer.addEventListener('scroll', () => {
                if (window.innerWidth <= 720) return; 
                handleScroll(scrollContainer.scrollTop);
            }, { passive: true });

            scrollContainer.addEventListener('click', () => {
                if (window.innerWidth < 1250 && !document.body.classList.contains('sidebars-hidden')) {
                    document.body.classList.add('sidebars-hidden');
                    rotateToggleIcons();
                }
            });
        }

        window.addEventListener('scroll', () => {
            if (window.innerWidth > 720) return; 
            handleScroll(window.scrollY || window.pageYOffset);
        }, { passive: true });

        const avatarContainer = document.getElementById('avatar-container');
        const dropdown = document.getElementById('avatar-dropdown');

        if (avatarContainer) {
            avatarContainer.onclick = (e) => { if (e.target.closest('.avatar-dropdown')) return; e.stopPropagation(); dropdown?.classList.toggle('show'); };
        }
        document.addEventListener('click', () => dropdown?.classList.remove('show'));

        document.body.addEventListener('click', function(e) {
            const target = e.target.closest('[data-module]');
            if (!target) return;

            const type = target.dataset.type;
            const folder = target.dataset.module;
            const isSmallScreen = window.innerWidth <= 1250; 

            if (folder === 'profile') {
                document.querySelectorAll('.main-nav .icon-btn, .page-icon').forEach(b => b.classList.remove('active'));
                if (isSmallScreen) {
                    document.querySelectorAll('.widget-nav .icon-btn, .mobile-bottom-nav .icon-btn, .mobile-widget-row .icon-btn').forEach(b => b.classList.remove('active-blue'));
                }

                if (window.navigateToPage) {
                    window.navigateToPage('home');
                }

                loadModule('page-content-placeholder', 'feed', 'profile');
                dropdown?.classList.remove('show');
            } 
            else if (target.closest('.widget-nav') || target.closest('.mobile-bottom-nav') || target.closest('.mobile-widget-row')) {
                if (target.classList.contains('active-blue') && !isSmallScreen) {
                    if (document.body.classList.contains('widget-only-hidden') || document.body.classList.contains('sidebars-hidden')) {
                        document.body.classList.remove('widget-only-hidden', 'sidebars-hidden');
                        rotateToggleIcons(); 
                    } else { 
                        document.body.classList.add('widget-only-hidden'); 
                        target.classList.remove('active-blue'); 
                        return;
                    }
                } else {
                    window.currentActiveWidget = folder; 
                    if (!isSmallScreen) { 
                        document.body.classList.remove('widget-only-hidden', 'sidebars-hidden'); 
                        rotateToggleIcons(); 
                    }
                    
                    document.querySelectorAll('.widget-nav .icon-btn, .mobile-bottom-nav .icon-btn, .mobile-widget-row .icon-btn').forEach(b => b.classList.remove('active-blue'));
                    target.classList.add('active-blue');
                    
                    if (isSmallScreen) {
                        document.querySelectorAll('.main-nav .icon-btn, .page-icon').forEach(b => b.classList.remove('active'));
                    }

                    loadModule(isSmallScreen ? 'page-content-placeholder' : 'sidebar-right-widget-placeholder', type, folder);
                }
            }
            else if (target.closest('.main-nav') || target.closest('.page-server-bar')) {
                document.querySelectorAll('.main-nav .icon-btn, .page-icon').forEach(b => b.classList.remove('active'));
                target.classList.add('active');

                if (isSmallScreen) {
                    document.querySelectorAll('.widget-nav .icon-btn, .mobile-bottom-nav .icon-btn, .mobile-widget-row .icon-btn').forEach(b => b.classList.remove('active-blue'));
                }

                loadModule('page-content-placeholder', type, folder);
            }
        });

        document.body.addEventListener('click', (e) => {
            if (e.target.closest('#sidebar-toggle, #mobile-sidebar-toggle')) {
                e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation();
                document.body.classList.toggle('sidebars-hidden');
                rotateToggleIcons(); 
            }
        }, true);
    }

    function renderFeedData(type) {
        const titleEl = document.getElementById('dynamic-feed-title');
        if (titleEl) titleEl.innerHTML = `<i class="fa-solid fa-layer-group"></i> ${ { 'home': 'Trang chủ', 'explore': 'Khám phá AI', 'favorite': 'Yêu thích', 'mixed': 'Hỗn hợp' }[type] || 'Feed' }`;
    }

    window.onload = () => {
        const baseUrl = "{{ url('/') }}";
        document.body.classList.add('widget-only-hidden');
        if (window.innerWidth < 1250) document.body.classList.add('sidebars-hidden'); else rotateToggleIcons();
        
        loadStatic('header-placeholder', `${baseUrl}/src/components/header/header.blade.php`);
        loadStatic('sidebar-left-placeholder', `${baseUrl}/src/components/sidebar-left/sidebar-left.blade.php`);
        loadStatic('sidebar-right-tools', `${baseUrl}/src/components/sidebar-right/sidebar-right.blade.php`);
        loadModule('page-content-placeholder', 'feed', 'home');
        loadModule('sidebar-right-widget-placeholder', 'widgets', 'notifications');
    };
    </script>
</body>
</html>