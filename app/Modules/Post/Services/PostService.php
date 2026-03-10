<?php

namespace App\Modules\Post\Services;

use App\Models\Post;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

class PostService
{
    /**
     * TẠO BÀI VIẾT MỚI (HOẶC CHIA SẺ LẠI BÀI VIẾT - REPOST)
     * Hàm này nhận vào Thông tin người đăng ($user) và Dữ liệu bài đăng ($data) từ Controller gửi sang.
     */
    public function create(User $user, array $data): Post
    {
        // Lệnh Post::create sẽ tạo 1 dòng mới trong bảng 'posts' ở Database
        return Post::create([
            'user_id'        => $user->id, // Tự động lấy ID của người đang đăng nhập
            'page_id'        => $data['page_id'] ?? null, // Nếu đăng vào 1 Trang (CLB/Khoa) thì lấy ID trang, không thì để trống
            'parent_post_id' => $data['parent_post_id'] ?? null, // Nếu đây là bài Share, lưu ID của bài viết gốc
            'title'          => $data['title'] ?? null, // Tiêu đề bài viết
            'content'        => $data['content'] ?? null, // Nội dung văn bản
            'media_path'     => $data['media_path'] ?? null, // Đường dẫn file ảnh/video nếu có upload
            'external_link'  => $data['external_link'] ?? null, // Link ngoài (nếu có)
            'visibility'     => $data['visibility'] ?? 'public', // Quyền riêng tư (Mặc định là public - công khai)
            'post_type'      => $data['post_type'] ?? 'post', // Phân loại bài: bài thường (post) hay sự kiện (event)...
            'tags'           => $data['tags'] ?? [], // Danh sách các thẻ tag (Laravel sẽ tự động chuyển mảng này thành chuỗi JSON để lưu)
            'scheduled_at'   => $data['scheduled_at'] ?? null, // Thời gian hẹn giờ đăng (nếu có)
        ]);
    }

    /**
     * LẤY DANH SÁCH BÀI VIẾT CHO TRANG CHỦ (NEWSFEED) CỦA USER
     * Bao gồm: Bài của chính User đó + Bài của các Trang (Page) mà User đang theo dõi.
     */
    public function getFeedForUser(User $user, int $perPage = 15): LengthAwarePaginator
    {
        // 1. Tìm tất cả các ID của các Trang (Page) mà User này đã bấm theo dõi
        $followedPageIds = $user->followedPages()->pluck('pages.id');

        // 2. Lọc bài viết từ Database
        return Post::with(['author', 'page', 'originalPost.author']) // Lấy kèm thông tin người đăng và trang để hiển thị cho nhanh
            ->published() // Chỉ lấy các bài đã xuất bản (không lấy bài nháp)
            ->where(function ($q) use ($user, $followedPageIds) {
                // Điều kiện lấy bài: Bài thuộc các Trang đã theo dõi HOẶC Bài do chính User này đăng
                $q->whereIn('page_id', $followedPageIds)
                  ->orWhere('user_id', $user->id);
            })
            ->latest() // Sắp xếp bài mới nhất lên đầu
            ->paginate($perPage); // Phân trang (mặc định 15 bài 1 trang để cuộn chuột cho mượt)
    }

    /**
     * LẤY DANH SÁCH BÀI VIẾT CHO TAB KHÁM PHÁ (EXPLORE)
     * Lấy tất cả các bài viết công khai trên toàn hệ thống để User xem.
     */
    public function getExplore(int $perPage = 15): LengthAwarePaginator
    {
        return Post::with(['author', 'page'])
            ->public() // Chỉ lấy bài viết ở chế độ Công khai (public)
            ->published() // Chỉ lấy bài đã xuất bản
            ->latest() // Mới nhất lên đầu
            ->paginate($perPage); // Phân trang
    }

    /**
     * TÍNH NĂNG THẢ TIM / BỎ THẢ TIM (TOGGLE LIKE)
     */
    public function toggleLike(User $user, Post $post): array
    {
        // Kiểm tra xem User này đã thả tim bài này trong Database chưa?
        $liked = $post->likes()->where('user_id', $user->id)->exists();

        if ($liked) {
            // Nếu đã thả tim rồi -> Bấm lần nữa là Hủy thả tim (Xóa khỏi DB)
            $post->likes()->detach($user->id);
        } else {
            // Nếu chưa thả tim -> Bấm vào là Thêm tim (Lưu vào DB)
            $post->likes()->attach($user->id);
        }

        // Trả về trạng thái hiện tại (đã like hay chưa) và Tổng số lượt tim của bài viết để Cập nhật lên giao diện
        return ['liked' => !$liked, 'count' => $post->likes()->count()];
    }

    /**
     * TÍNH NĂNG LƯU BÀI VIẾT / BỎ LƯU (TOGGLE SAVE)
     */
    public function toggleSave(User $user, Post $post): array
    {
        // Tương tự như thả tim, kiểm tra xem đã lưu bài này chưa
        $saved = $post->saves()->where('user_id', $user->id)->exists();

        if ($saved) {
            // Đã lưu rồi -> Xóa khỏi danh sách lưu
            $post->saves()->detach($user->id);
        } else {
            // Chưa lưu -> Thêm vào danh sách lưu
            $post->saves()->attach($user->id);
        }

        // Trả về trạng thái để giao diện đổi màu nút Lưu
        return ['saved' => !$saved];
    }

    /**
     * XÓA BÀI VIẾT (SOFT DELETE)
     * Xóa mềm: Bài viết không mất hẳn trong CSDL mà chỉ bị ẩn đi. 
     * Nếu có ai đó đã share (repost) bài này, bài share sẽ hiện "Nội dung không khả dụng".
     */
    public function delete(Post $post): void
    {
        $post->delete();
    }
}