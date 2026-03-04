<link rel="stylesheet" href="{{ asset('views/src/modules/feed/feed-controller/feed-controller.css') }}">

<div class="feed-main-container">
    
    <div class="story-wrapper card-bg">
        <div class="story-item add-story">
            <div class="story-avatar">
                <img src="https://ui-avatars.com/api/?name=Đạt&background=003366&color=fff" alt="Sếp Đạt">
            </div>
            <div class="add-icon-overlay"><i class="fa-solid fa-plus"></i></div>
        </div>
        <div class="story-item"><div class="story-avatar has-story"><img src="https://i.pravatar.cc/150?img=11" alt="User"></div></div>
        <div class="story-item"><div class="story-avatar has-story"><img src="https://i.pravatar.cc/150?img=12" alt="User"></div></div>
        <div class="story-item"><div class="story-avatar has-story"><img src="https://i.pravatar.cc/150?img=33" alt="User"></div></div>
        <div class="story-item"><div class="story-avatar has-story"><img src="https://i.pravatar.cc/150?img=44" alt="User"></div></div>
        <div class="story-item"><div class="story-avatar has-story"><img src="https://i.pravatar.cc/150?img=55" alt="User"></div></div>
        <div class="story-item"><div class="story-avatar has-story"><img src="https://i.pravatar.cc/150?img=66" alt="User"></div></div>
    </div>

    <div id="feed-filters-placeholder"></div>

    <div class="post-list-area" id="main-feed-posts">
        </div>
</div>

<script>
    (async function loadFeedSharedComponents() {
        try {
            // 1. Nhúng Bộ Lọc
            const resFilters = await fetch('{{ url('src/components/feed-filters/feed-filters.blade.php') }}');
            if (resFilters.ok) {
                const html = await resFilters.text();
                const placeholder = document.getElementById('feed-filters-placeholder');
                if (placeholder) {
                    placeholder.innerHTML = html;
                }
            }

            // 2. Nhúng và đổ dữ liệu bài viết từ post-item.blade.php
            const resItem = await fetch('{{ url('src/components/post-item/post-item.blade.php') }}');
            if (resItem.ok) {
                const template = await resItem.text();
                const container = document.getElementById('main-feed-posts');
                if (!container) return;

                // Dữ liệu mẫu bài viết
                const feedPosts = [
                    {
                        name: "BCH HỘI SINH VIÊN HỌC VIỆN NGÂN HÀNG",
                        avatar: "https://ui-avatars.com/api/?name=HSV&background=e4e6eb&color=000",
                        time: "2 giờ trước • 🌍",
                        content: "Căn cứ công văn số 3094-CV/TĐTN-CTĐ&TTN của Thành đoàn Hà Nội, BCH Hội Sinh viên triển khai Cuộc thi tìm hiểu Nghị quyết Đại hội đại biểu Đảng bộ thành phố Hà Nội lần thứ XVIII...",
                        image: "https://images.unsplash.com/photo-1557683311-eac922347aa1?auto=format&fit=crop&q=80&w=800&h=500"
                    },
                    {
                        name: "BCH HỘI SINH VIÊN HỌC VIỆN NGÂN HÀNG",
                        avatar: "https://ui-avatars.com/api/?name=HSV&background=e4e6eb&color=000",
                        time: "5 giờ trước • 🌍",
                        content: "Thông báo về việc đăng ký tham gia các câu lạc bộ học thuật kỳ II năm học 2025-2026. Sinh viên chú ý theo dõi lịch trình cụ thể.",
                        image: "https://images.unsplash.com/photo-1540575467063-178a50c2df87?q=80&w=800"
                    },
                    {
                        name: "BCH HỘI SINH VIÊN HỌC VIỆN NGÂN HÀNG",
                        avatar: "https://ui-avatars.com/api/?name=HSV&background=e4e6eb&color=000",
                        time: "5 giờ trước • 🌍",
                        content: "Thông báo về việc đăng ký tham gia các câu lạc bộ học thuật kỳ II năm học 2025-2026. Sinh viên chú ý theo dõi lịch trình cụ thể.",
                        image: "https://images.unsplash.com/photo-1540575467063-178a50c2df87?q=80&w=800"
                    },
                    {
                        name: "BCH HỘI SINH VIÊN HỌC VIỆN NGÂN HÀNG",
                        avatar: "https://ui-avatars.com/api/?name=HSV&background=e4e6eb&color=000",
                        time: "5 giờ trước • 🌍",
                        content: "Thông báo về việc đăng ký tham gia các câu lạc bộ học thuật kỳ II năm học 2025-2026. Sinh viên chú ý theo dõi lịch trình cụ thể.",
                        image: "https://images.unsplash.com/photo-1540575467063-178a50c2df87?q=80&w=800"
                    },
                    {
                        name: "BCH HỘI SINH VIÊN HỌC VIỆN NGÂN HÀNG",
                        avatar: "https://ui-avatars.com/api/?name=HSV&background=e4e6eb&color=000",
                        time: "5 giờ trước • 🌍",
                        content: "Thông báo về việc đăng ký tham gia các câu lạc bộ học thuật kỳ II năm học 2025-2026. Sinh viên chú ý theo dõi lịch trình cụ thể.",
                        image: "https://images.unsplash.com/photo-1540575467063-178a50c2df87?q=80&w=800"
                    },
                    {
                        name: "BCH HỘI SINH VIÊN HỌC VIỆN NGÂN HÀNG",
                        avatar: "https://ui-avatars.com/api/?name=HSV&background=e4e6eb&color=000",
                        time: "5 giờ trước • 🌍",
                        content: "Thông báo về việc đăng ký tham gia các câu lạc bộ học thuật kỳ II năm học 2025-2026. Sinh viên chú ý theo dõi lịch trình cụ thể.",
                        image: "https://images.unsplash.com/photo-1540575467063-178a50c2df87?q=80&w=800"
                    },
                    {
                        name: "BCH HỘI SINH VIÊN HỌC VIỆN NGÂN HÀNG",
                        avatar: "https://ui-avatars.com/api/?name=HSV&background=e4e6eb&color=000",
                        time: "5 giờ trước • 🌍",
                        content: "Thông báo về việc đăng ký tham gia các câu lạc bộ học thuật kỳ II năm học 2025-2026. Sinh viên chú ý theo dõi lịch trình cụ thể.",
                        image: "https://images.unsplash.com/photo-1540575467063-178a50c2df87?q=80&w=800"
                    },
                    {
                        name: "BCH HỘI SINH VIÊN HỌC VIỆN NGÂN HÀNG",
                        avatar: "https://ui-avatars.com/api/?name=HSV&background=e4e6eb&color=000",
                        time: "5 giờ trước • 🌍",
                        content: "Thông báo về việc đăng ký tham gia các câu lạc bộ học thuật kỳ II năm học 2025-2026. Sinh viên chú ý theo dõi lịch trình cụ thể.",
                        image: "https://images.unsplash.com/photo-1540575467063-178a50c2df87?q=80&w=800"
                    },{
                        name: "BCH HỘI SINH VIÊN HỌC VIỆN NGÂN HÀNG",
                        avatar: "https://ui-avatars.com/api/?name=HSV&background=e4e6eb&color=000",
                        time: "5 giờ trước • 🌍",
                        content: "Thông báo về việc đăng ký tham gia các câu lạc bộ học thuật kỳ II năm học 2025-2026. Sinh viên chú ý theo dõi lịch trình cụ thể.",
                        image: "https://images.unsplash.com/photo-1540575467063-178a50c2df87?q=80&w=800"
                    }

                ];

                container.innerHTML = ''; 

                feedPosts.forEach(data => {
                    const tempDiv = document.createElement('div');
                    tempDiv.innerHTML = template;

                    // Điền dữ liệu vào các thẻ class tương ứng trong post-item.blade.php
                    const nameEl = tempDiv.querySelector('.post-data-name');
                    const avatarEl = tempDiv.querySelector('.post-data-avatar');
                    const timeEl = tempDiv.querySelector('.post-data-time');
                    const contentEl = tempDiv.querySelector('.post-data-content');
                    const mediaCont = tempDiv.querySelector('.post-data-media-container');
                    const imgEl = tempDiv.querySelector('.post-data-image');

                    if(nameEl) nameEl.innerText = data.name;
                    if(avatarEl) avatarEl.src = data.avatar;
                    if(timeEl) timeEl.innerText = data.time;
                    if(contentEl) contentEl.innerText = data.content;
                    
                    if(data.image && mediaCont && imgEl) {
                        mediaCont.style.display = 'block';
                        imgEl.src = data.image;
                    }

                    // Chèn nội dung vào danh sách
                    while (tempDiv.firstChild) {
                        container.appendChild(tempDiv.firstChild);
                    }
                });
            }

        } catch (err) {
            console.error("Lỗi tải component vào Feed:", err);
        }
    })();
</script>