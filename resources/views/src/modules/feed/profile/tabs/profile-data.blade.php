<div class="info-card" style="width: 100%; margin-top: 20px;">
    <div class="info-card-header" style="margin-bottom: 20px; border-bottom: 1px solid #f0f2f5; padding-bottom: 15px;">
        <h3 style="font-size: 16px; color: #1c1e21; font-weight: 700; display: flex; align-items: center; gap: 8px; margin: 0;">
            <i class="fa-solid fa-bookmark" style="color: #f5a623;"></i> LƯU TRỮ BÀI VIẾT & TÀI LIỆU
        </h3>
    </div>

    @php
        // Truy vấn danh sách bài viết đã lưu của user đang xem profile
        $savedPosts = \Illuminate\Support\Facades\DB::table('post_saves')
            ->join('posts', 'post_saves.post_id', '=', 'posts.id')
            ->leftJoin('pages', 'posts.page_id', '=', 'pages.id') // Lấy tên Page (nếu có)
            ->leftJoin('users', 'posts.user_id', '=', 'users.id') // Lấy tên User tác giả (nếu là bài cá nhân)
            ->where('post_saves.user_id', $user->id)
            ->select(
                'posts.id',
                'posts.title',
                'posts.post_type',
                'pages.name as page_name',
                'users.full_name as author_name'
            )
            ->orderBy('post_saves.created_at', 'desc')
            ->get();
    @endphp

    <div class="info-expanded-layout">
        <div class="info-expanded-sidebar">
            <button class="info-tab-btn active">Tất cả mục đã lưu ({{ $savedPosts->count() }})</button>
            <button class="info-tab-btn">Bài viết chuyên ngành</button>
            <button class="info-tab-btn">Tài liệu tham khảo</button>
            <button class="info-tab-btn">Source Code / Đồ án</button>
        </div>
        
        <div class="info-expanded-content">
            @if($savedPosts->count() > 0)
                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px;">
                    @foreach($savedPosts as $post)
                        <div style="border: 1px solid #e4e6eb; border-radius: 8px; padding: 15px; position: relative; background: #fff; transition: 0.2s; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
                            <i class="fa-solid fa-bookmark" style="position: absolute; top: 15px; right: 15px; color: #1877f2; font-size: 18px; cursor: pointer;" title="Bỏ lưu bài viết"></i>
                            
                            <div style="font-size: 12px; color: #8e8e8e; margin-bottom: 5px; font-weight: 500;">
                                Từ: {{ $post->page_name ?? $post->author_name }}
                            </div>
                            
                            <div style="font-weight: 600; color: #1c1e21; font-size: 14px; margin-bottom: 12px; padding-right: 20px; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                {{ $post->title ?? 'Bài viết không có tiêu đề' }}
                            </div>
                            
                            <button onclick="window.location.href='/post/{{ $post->id }}'" style="background: transparent; color: #1877f2; border: 1px solid #1877f2; padding: 6px 12px; border-radius: 6px; font-size: 12px; cursor: pointer; font-weight: 600; transition: 0.2s;" onmouseover="this.style.background='#e7f3ff'" onmouseout="this.style.background='transparent'">
                                Xem bài viết
                            </button>
                        </div>
                    @endforeach
                </div>
            @else
                <div style="text-align: center; padding: 50px 20px; color: #8e8e8e; border: 1px dashed #ccd0d5; border-radius: 12px; background: #fafafa;">
                    <i class="fa-regular fa-bookmark" style="font-size: 40px; margin-bottom: 15px; opacity: 0.5;"></i>
                    <p style="margin: 0; font-size: 15px; font-weight: 500;">Bạn chưa lưu bài viết hay tài liệu nào.</p>
                </div>
            @endif
        </div>
    </div>
</div>