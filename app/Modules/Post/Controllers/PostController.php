<?php

namespace App\Modules\Post\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Modules\Post\Requests\StorePostRequest;
use App\Modules\Post\Services\PostService;
use Illuminate\Http\Request;

class PostController extends Controller
{
    // Cú pháp khởi tạo nhanh của PHP 8: tự động gán PostService vào biến $postService
    public function __construct(private PostService $postService) {}

    /**
     * XỬ LÝ ĐĂNG BÀI VIẾT MỚI
     */
    public function store(StorePostRequest $request)
    {
        // 1. Lấy toàn bộ dữ liệu đã được kiểm duyệt (bao gồm nội dung, link form...)
        $data = $request->validated();

        // 2. Xử lý file đính kèm (Ảnh/Video) nếu có
        // Lưu ý: Ở file giao diện HTML, thẻ input upload file phải đặt name="media"
        if ($request->hasFile('media')) {
            // Lưu file vào thư mục 'posts' trong ổ cứng và lấy đường dẫn lưu vào database
            $data['media_path'] = $request->file('media')->store('posts', 'public');
        }

        // 3. XỬ LÝ LỖI Ở ĐÂY: Lấy User đang đăng nhập, NẾU CHƯA CÓ thì lấy tạm User đầu tiên trong Database
        $user = $request->user() ?? \App\Models\User::first();

        // 4. Gọi PostService để tạo bài viết (Gắn kèm ID của người dùng ở bước 3)
        $post = $this->postService->create($user, $data);

        // 5. Trả về trang cũ kèm thông báo
        return redirect()->back()->with('success', 'Bài viết đã được đăng thành công!');
    }

    /**
     * XEM CHI TIẾT 1 BÀI VIẾT
     */
    public function show(Post $post)
    {
        // Kiểm tra xem User này có quyền xem bài viết không (ví dụ bài riêng tư thì không cho xem)
        $this->authorize('view', $post);
        
        // Tải kèm các thông tin liên quan để hiển thị (Người đăng, Trang, Bài gốc nếu là share, Bình luận...)
        $post->load(['author', 'page', 'originalPost.author', 'comments.author', 'likes']);

        return view('post.show', compact('post'));
    }

    /**
     * CẬP NHẬT/SỬA BÀI VIẾT
     */
    public function update(Request $request, Post $post)
    {
        // Chỉ tác giả bài viết hoặc Admin mới có quyền sửa
        $this->authorize('update', $post);

        // Lấy các trường cho phép sửa (đã bổ sung thêm external_link để có thể sửa link form)
        $post->update($request->only(['title', 'content', 'visibility', 'tags', 'external_link']));

        return redirect()->back()->with('success', 'Đã cập nhật bài viết thành công.');
    }

    /**
     * XÓA BÀI VIẾT
     */
    public function destroy(Post $post)
    {
        // Kiểm tra quyền xóa
        $this->authorize('delete', $post);
        
        // Gọi Service thực hiện xóa
        $this->postService->delete($post);

        return redirect()->back()->with('success', 'Đã xóa bài viết.');
    }

    /**
     * THẢ TIM / HỦY TIM
     */
    public function toggleLike(Post $post)
    {
        $this->authorize('view', $post);
        
        // Gọi Service xử lý logic thả tim
        $result = $this->postService->toggleLike(auth()->user(), $post);

        // Trả về JSON để giao diện Frontend tự đổi màu nút Like mà không cần tải lại trang web
        return response()->json($result);
    }

    /**
     * LƯU / BỎ LƯU BÀI VIẾT VÀO DANH SÁCH ĐỌC SAU
     */
    public function toggleSave(Post $post)
    {
        $this->authorize('view', $post);
        
        $result = $this->postService->toggleSave(auth()->user(), $post);

        return response()->json($result);
    }

    /**
     * CHIA SẺ LẠI BÀI VIẾT (REPOST)
     */
    public function repost(Post $post)
    {
        // Kiểm tra quyền share
        $this->authorize('repost', $post);

        // Gọi Service tạo bài viết mới, trỏ parent_post_id về ID của bài viết gốc
        $repost = $this->postService->create(auth()->user(), [
            'parent_post_id' => $post->id,
            'content' => request('content'), // Nội dung cap mà người share gõ thêm
            'visibility' => request('visibility', 'public'), // Quyền riêng tư của bài share
        ]);

        return redirect()->back()->with('success', 'Đã chia sẻ bài viết thành công!');
    }
}