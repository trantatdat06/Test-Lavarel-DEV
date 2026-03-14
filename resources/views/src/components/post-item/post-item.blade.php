<style>
    .post-card { background: #fff; border-radius: 12px; box-shadow: 0 1px 4px rgba(0,0,0,0.1); border: 1px solid #e4e6eb; margin-bottom: 16px; overflow: hidden; padding-top: 12px; font-family: inherit; }
    .post-header { display: flex; justify-content: space-between; align-items: flex-start; padding: 0 16px; margin-bottom: 12px; }
    .post-author-info { display: flex; gap: 10px; }
    .author-avatar-post { width: 40px; height: 40px; border-radius: 50%; object-fit: cover; }
    .author-meta-post { display: flex; flex-direction: column; }
    .author-name-post { font-weight: 700; font-size: 15px; color: #050505; display: flex; align-items: center; gap: 4px; }
    .post-time-post { font-size: 13px; color: #65676b; display: flex; align-items: center; gap: 4px; }
    .post-body-post { padding: 0 16px; }
    .post-text-post { font-size: 15px; color: #050505; line-height: 1.4; margin-bottom: 12px; white-space: pre-wrap; }
    .see-more-post { color: #65676b; cursor: pointer; font-weight: 600; }
    .post-media-post { width: 100%; border-top: 1px solid #f0f2f5; border-bottom: 1px solid #f0f2f5; background: #f0f2f5; display: none; }
    .post-media-post img { width: 100%; display: block; max-height: 600px; object-fit: cover; }
    .post-footer-post { display: flex; justify-content: space-between; padding: 4px 12px; border-top: 1px solid #f0f2f5; }
    .interaction-group-post { display: flex; gap: 4px; }
    .action-btn-post { background: transparent; border: none; padding: 10px 12px; border-radius: 6px; color: #050505; font-size: 20px; cursor: pointer; display: flex; align-items: center; transition: 0.2s; }
    .action-btn-post:hover { background: #f2f2f2; }
    .action-btn-post.active { color: #e41e3f; }
</style>

<div class="post-card">
    <div class="post-header">
        <div class="post-author-info">
            <img class="author-avatar-post post-data-avatar" src="" alt="Avatar">
            <div class="author-meta-post">
                <div class="author-name-post">
                    <span class="post-data-name">Người dùng</span>
                    <i class="fa-solid fa-circle-check" style="color: #1877f2; font-size: 13px;"></i>
                </div>
                <div class="post-time-post">
                    <span class="post-data-time">Vừa xong</span>
                    <span>•</span>
                    <i class="fa-solid fa-earth-americas" style="font-size: 11px;"></i>
                </div>
            </div>
        </div>
        <button class="action-btn-post"><i class="fa-solid fa-ellipsis"></i></button>
    </div>

    <div class="post-body-post">
        <p class="post-text-post">
            <span class="post-data-content">Nội dung...</span>
            <span class="see-more-post">Xem thêm</span>
        </p>

        <div class="post-data-form-container" style="display: none; margin: 12px 0; padding: 12px 16px; background: #f8fafc; border-radius: 12px; border: 1px solid #e2e8f0; align-items: center; justify-content: space-between; gap: 15px;">
            <div style="display: flex; align-items: center; gap: 12px;">
                <div style="width: 40px; height: 40px; background: #4f46e5; color: white; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 18px;">
                    <i class="fa-solid fa-file-signature"></i>
                </div>
                <div>
                    <div style="font-weight: 700; font-size: 14px; color: #0f172a;">Đính kèm Hoạt động</div>
                    <div style="font-size: 12px; color: #64748b;">Nhấn vào nút bên cạnh để xem chi tiết</div>
                </div>
            </div>
            <a href="#" class="post-data-form-link" target="_blank" 
               style="background: #4f46e5; color: white; padding: 8px 16px; border-radius: 6px; font-weight: 600; font-size: 13px; text-decoration: none; white-space: nowrap; transition: 0.2s;">
                Xem chi tiết
            </a>
        </div>
    </div>
    
    <div class="post-media-post post-data-media-container" style="display: none;">
        <img class="post-data-image" src="" alt="Post content">
    </div>

    <div class="post-footer-post">
        <div class="interaction-group-post">
            <button class="action-btn-post" onclick="this.classList.toggle('active')"><i class="fa-regular fa-heart"></i></button>
            <button class="action-btn-post"><i class="fa-regular fa-comment"></i></button>
            <button class="action-btn-post"><i class="fa-solid fa-share"></i></button>
        </div>
        <div class="interaction-group-post">
            <button class="action-btn-post"><i class="fa-regular fa-bookmark"></i></button>
        </div>
    </div>
</div>