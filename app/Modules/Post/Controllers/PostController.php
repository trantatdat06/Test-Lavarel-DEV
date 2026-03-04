<?php

namespace App\Modules\Post\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Modules\Post\Requests\StorePostRequest;
use App\Modules\Post\Services\PostService;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function __construct(private PostService $postService) {}

    public function store(StorePostRequest $request)
    {
        $data = $request->validated();

        // Handle file upload
        if ($request->hasFile('media')) {
            $data['media_path'] = $request->file('media')->store('posts', 'public');
        }

        $post = $this->postService->create($request->user(), $data);

        return redirect()->back()->with('success', 'Bài viết đã được đăng!');
    }

    public function show(Post $post)
    {
        $this->authorize('view', $post);
        $post->load(['author', 'page', 'originalPost.author', 'comments.author', 'likes']);

        return view('post.show', compact('post'));
    }

    public function update(Request $request, Post $post)
    {
        $this->authorize('update', $post);

        $post->update($request->only(['title', 'content', 'visibility', 'tags']));

        return redirect()->back()->with('success', 'Đã cập nhật bài viết.');
    }

    public function destroy(Post $post)
    {
        $this->authorize('delete', $post);
        $this->postService->delete($post);

        return redirect()->back()->with('success', 'Đã xóa bài viết.');
    }

    public function toggleLike(Post $post)
    {
        $this->authorize('view', $post);
        $result = $this->postService->toggleLike(auth()->user(), $post);

        return response()->json($result);
    }

    public function toggleSave(Post $post)
    {
        $this->authorize('view', $post);
        $result = $this->postService->toggleSave(auth()->user(), $post);

        return response()->json($result);
    }

    public function repost(Post $post)
    {
        $this->authorize('repost', $post);

        $repost = $this->postService->create(auth()->user(), [
            'parent_post_id' => $post->id,
            'visibility' => 'public',
        ]);

        return redirect()->back()->with('success', 'Đã chia sẻ bài viết!');
    }
}