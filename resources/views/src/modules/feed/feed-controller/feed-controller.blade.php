<link rel="stylesheet" href="{{ asset('views/src/modules/feed/feed-controller/feed-controller.css') }}">

<div class="feed-main-container">
    
    <div class="story-wrapper card-bg" style="margin-bottom: 20px;">
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
            // Nhúng Bộ Lọc
            const resFilters = await fetch('{{ url('src/components/feed-filters/feed-filters.blade.php') }}');
            if (resFilters.ok) {
                const html = await resFilters.text();
                const placeholder = document.getElementById('feed-filters-placeholder');
                if (placeholder) {
                    placeholder.innerHTML = html;
                }
            }

            // Nhúng và đổ dữ liệu bài viết từ post-item.blade.php
            const resItem = await fetch('{{ url('src/components/post-item/post-item.blade.php') }}');
            if (resItem.ok) {
                const template = await resItem.text();
                const container = document.getElementById('main-feed-posts');
                if (!container) return;

                // Lấy DỮ LIỆU THẬT từ Database của Laravel truyền sang Javascript
                <?php
                    $realPosts = \App\Models\Post::with('author')->latest()->take(20)->get();
                    $feedArray = [];
                    foreach($realPosts as $post) {
                        $feedArray[] = [
                            'name' => $post->author->name ?? 'Người dùng',
                            'avatar' => $post->author->avatar ?? 'https://ui-avatars.com/api/?name=User&background=random',
                            'time' => $post->created_at->diffForHumans(),
                            'content' => $post->content,
                            'image' => $post->media_path ? asset('storage/' . $post->media_path) : null,
                            'external_link' => $post->external_link
                        ];
                    }
                ?>
                
                const feedPosts = @json($feedArray);

                // Nếu Database rỗng (không có bài nào)
                if (feedPosts.length === 0) {
                    feedPosts.push({ 
                        name: "Hệ thống", 
                        avatar: "https://ui-avatars.com/api/?name=Admin&background=1877f2&color=fff", 
                        time: "Vừa xong", 
                        content: "Chào mừng bạn đến với Mạng xã hội học tập! Hãy đăng bài viết đầu tiên của bạn nhé.", 
                        image: null, 
                        external_link: null 
                    });
                }

                container.innerHTML = ''; 

                feedPosts.forEach(data => {
                    const tempDiv = document.createElement('div');
                    tempDiv.innerHTML = template;

                    const nameEl = tempDiv.querySelector('.post-data-name');
                    const avatarEl = tempDiv.querySelector('.post-data-avatar');
                    const timeEl = tempDiv.querySelector('.post-data-time');
                    const contentEl = tempDiv.querySelector('.post-data-content');
                    const mediaCont = tempDiv.querySelector('.post-data-media-container');
                    const imgEl = tempDiv.querySelector('.post-data-image');
                    
                    const formContainer = tempDiv.querySelector('.post-data-form-container');
                    const formLink = tempDiv.querySelector('.post-data-form-link');

                    if(nameEl) nameEl.innerText = data.name;
                    if(avatarEl) avatarEl.src = data.avatar;
                    if(timeEl) timeEl.innerText = data.time;
                    if(contentEl) contentEl.innerText = data.content;
                    
                    if(data.image && mediaCont && imgEl) {
                        mediaCont.style.display = 'block';
                        imgEl.src = data.image;
                    }

                    if(data.external_link && formContainer && formLink) {
                        formContainer.style.display = 'flex';
                        formLink.href = data.external_link;
                    }

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