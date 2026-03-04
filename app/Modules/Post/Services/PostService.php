<?php

namespace App\Modules\Post\Services;

use App\Models\Post;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

class PostService
{
    /**
     * Create a new post (or repost).
     */
    public function create(User $user, array $data): Post
    {
        return Post::create([
            'user_id'        => $user->id,
            'page_id'        => $data['page_id'] ?? null,
            'parent_post_id' => $data['parent_post_id'] ?? null,
            'title'          => $data['title'] ?? null,
            'content'        => $data['content'] ?? null,
            'media_path'     => $data['media_path'] ?? null,
            'external_link'  => $data['external_link'] ?? null,
            'visibility'     => $data['visibility'] ?? 'public',
            'post_type'      => $data['post_type'] ?? 'post',
            'tags'           => $data['tags'] ?? [],
            'scheduled_at'   => $data['scheduled_at'] ?? null,
        ]);
    }

    /**
     * Get feed for the given user (followed pages + own posts).
     */
    public function getFeedForUser(User $user, int $perPage = 15): LengthAwarePaginator
    {
        $followedPageIds = $user->followedPages()->pluck('pages.id');

        return Post::with(['author', 'page', 'originalPost.author'])
            ->published()
            ->where(function ($q) use ($user, $followedPageIds) {
                $q->whereIn('page_id', $followedPageIds)
                  ->orWhere('user_id', $user->id);
            })
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Get public posts for the Explore tab.
     */
    public function getExplore(int $perPage = 15): LengthAwarePaginator
    {
        return Post::with(['author', 'page'])
            ->public()
            ->published()
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Toggle like on a post.
     */
    public function toggleLike(User $user, Post $post): array
    {
        $liked = $post->likes()->where('user_id', $user->id)->exists();

        if ($liked) {
            $post->likes()->detach($user->id);
        } else {
            $post->likes()->attach($user->id);
        }

        return ['liked' => !$liked, 'count' => $post->likes()->count()];
    }

    /**
     * Toggle save on a post.
     */
    public function toggleSave(User $user, Post $post): array
    {
        $saved = $post->saves()->where('user_id', $user->id)->exists();

        if ($saved) {
            $post->saves()->detach($user->id);
        } else {
            $post->saves()->attach($user->id);
        }

        return ['saved' => !$saved];
    }

    /**
     * Soft delete a post. Reposts will show "Content unavailable".
     */
    public function delete(Post $post): void
    {
        $post->delete();
    }
}