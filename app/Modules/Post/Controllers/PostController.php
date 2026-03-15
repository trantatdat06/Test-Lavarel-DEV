<?php

namespace App\Modules\Post\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Event;
use App\Models\Form;
use App\Modules\Post\Requests\StorePostRequest;
use App\Modules\Post\Services\PostService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PostController extends Controller
{
    // Cú pháp khởi tạo nhanh của PHP 8: tự động gán PostService vào biến $postService
    public function __construct(private PostService $postService) {}

    /**
     * XỬ LÝ ĐĂNG BÀI VIẾT MỚI (Tích hợp đính kèm Sự kiện & Biểu mẫu)
     */
    public function store(StorePostRequest $request)
    {
        // 1. Lấy dữ liệu cơ bản của bài viết đã được kiểm duyệt trong StorePostRequest
        $data = $request->validated();

        // 2. Validate bổ sung trực tiếp cho các trường Đính kèm (Luồng 2)
        $request->validate([
            'has_event'           => 'nullable|in:0,1',
            'attached_event_id'   => 'required_if:has_event,1|nullable|exists:events,id',
            'has_form'            => 'nullable|in:0,1',
            'attached_form_id'    => 'required_if:has_form,1|nullable|exists:forms,id',
        ], [
            'attached_event_id.required_if' => 'Vui lòng chọn một Sự kiện từ danh sách để đính kèm.',
            'attached_event_id.exists'      => 'Sự kiện này không tồn tại hoặc đã bị xóa.',
            'attached_form_id.required_if'  => 'Vui lòng chọn một Biểu mẫu từ danh sách để đính kèm.',
            'attached_form_id.exists'       => 'Biểu mẫu này không tồn tại hoặc đã bị xóa.',
        ]);

        // 3. Xử lý file đính kèm (Ảnh/Video)
        if ($request->hasFile('media')) {
            $data['media_path'] = $request->file('media')->store('posts/media', 'public');
        }

        // Lấy User đang đăng nhập (hoặc User mặc định nếu đang test)
        $user = $request->user() ?? \App\Models\User::first();

        // Sử dụng Transaction để đảm bảo tính toàn vẹn dữ liệu
        try {
            DB::beginTransaction();

            // 4. Gọi PostService để tạo bài viết (Giữ nguyên luồng chuẩn của bạn)
            $post = $this->postService->create($user, $data);

            // 5. MÓC NỐI SỰ KIỆN: Nếu Admin bật "has_event" và có chọn ID Sự kiện
            if ($request->input('has_event') == '1' && $request->filled('attached_event_id')) {
                $event = Event::find($request->input('attached_event_id'));
                if ($event) {
                    $event->post_id = $post->id; // Gắn ID bài viết vào Sự kiện
                    $event->save();
                }
            }

            // 6. MÓC NỐI BIỂU MẪU: Nếu Admin bật "has_form" và có chọn ID Biểu mẫu
            if ($request->input('has_form') == '1' && $request->filled('attached_form_id')) {
                $form = Form::find($request->input('attached_form_id'));
                if ($form) {
                    $form->post_id = $post->id; // Gắn ID bài viết vào Form
                    $form->save();
                }
            }

            DB::commit();
            return redirect()->back()->with('success', 'Bài viết đã được đăng thành công kèm theo biểu mẫu/sự kiện!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withErrors(['error' => 'Lỗi hệ thống không thể đăng bài: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * XEM CHI TIẾT 1 BÀI VIẾT
     */
    public function show(Post $post)
    {
        $this->authorize('view', $post);
        
        $post->load(['author', 'page', 'originalPost.author', 'comments.author', 'likes']);

        return view('post.show', compact('post'));
    }

    /**
     * CẬP NHẬT/SỬA BÀI VIẾT
     */
    public function update(Request $request, Post $post)
    {
        $this->authorize('update', $post);

        $post->update($request->only(['title', 'content', 'visibility', 'tags', 'external_link']));

        return redirect()->back()->with('success', 'Đã cập nhật bài viết thành công.');
    }

    /**
     * XÓA BÀI VIẾT
     */
    public function destroy(Post $post)
    {
        $this->authorize('delete', $post);
        
        $this->postService->delete($post);

        return redirect()->back()->with('success', 'Đã xóa bài viết.');
    }

    /**
     * THẢ TIM / HỦY TIM
     */
    public function toggleLike(Post $post)
    {
        $this->authorize('view', $post);
        
        $result = $this->postService->toggleLike(auth()->user(), $post);

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
        $this->authorize('repost', $post);

        $repost = $this->postService->create(auth()->user(), [
            'parent_post_id' => $post->id,
            'content' => request('content'), 
            'visibility' => request('visibility', 'public'), 
        ]);

        return redirect()->back()->with('success', 'Đã chia sẻ bài viết thành công!');
    }
}