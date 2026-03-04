<div class="profile-right-col animate-fade-in-up" style="max-width: 680px; margin: 0 auto;">
    <div class="profile-feed-header">
        <div id="shared-post-composer-placeholder"></div>
        <div id="shared-feed-filters-placeholder" style="margin-top: 15px;"></div>
    </div>
    <div id="profile-posts-list"></div>
</div>

<script>
    (async function loadPostsOnly() {
        try {
            const [resComp, resFilt, resItem] = await Promise.all([
                fetch('{{ url('src/components/post-composer/post-composer.blade.php') }}'),
                fetch('{{ url('src/components/feed-filters/feed-filters.blade.php') }}'),
                fetch('{{ url('src/components/post-item/post-item.blade.php') }}')
            ]);
            
            if(resComp.ok) {
                const html = await resComp.text();
                const placeholder = document.getElementById('shared-post-composer-placeholder');
                placeholder.innerHTML = html;
                if(typeof window.executeDynamicScripts === 'function') window.executeDynamicScripts(placeholder);
            }
            if(resFilt.ok) document.getElementById('shared-feed-filters-placeholder').innerHTML = await resFilt.text();
            
            if(resItem.ok) {
                const template = await resItem.text();
                const container = document.getElementById('profile-posts-list');
                const samplePosts = [{
                    name: "Trang Dự Án AI",
                    avatar: "https://ui-avatars.com/api/?name=AI&background=4a66f0&color=fff",
                    time: "Vừa xong",
                    content: "Dưới đây là danh sách bài viết chi tiết được tách riêng ra một tab để sếp dễ quản lý.",
                    image: ""
                }];

                samplePosts.forEach(data => {
                    const tempDiv = document.createElement('div');
                    tempDiv.innerHTML = template;
                    tempDiv.querySelector('.post-data-name').innerText = data.name;
                    tempDiv.querySelector('.post-data-avatar').src = data.avatar;
                    tempDiv.querySelector('.post-data-time').innerText = data.time;
                    tempDiv.querySelector('.post-data-content').innerText = data.content;
                    while (tempDiv.firstChild) { container.appendChild(tempDiv.firstChild); }
                });
            }
        } catch (err) { console.error("Lỗi tải tab Bài viết:", err); }
    })();
</script>