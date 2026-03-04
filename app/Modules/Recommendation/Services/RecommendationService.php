<?php

namespace App\Modules\Recommendation\Services;

use App\Models\Page;
use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Collection;

class RecommendationService
{
    /**
     * Recommend pages to follow based on faculty, event history, and existing follows.
     */
    public function recommendPages(User $user, int $limit = 10): Collection
    {
        $followedPageIds = $user->followedPages()->pluck('pages.id');

        // Pages in the same faculty
        $query = Page::where('type', 'public')
            ->whereNotIn('id', $followedPageIds)
            ->select('pages.*');

        // Boost pages that have been joined by people in same faculty
        if ($user->faculty_id) {
            $query->orderByRaw("
                CASE WHEN EXISTS (
                    SELECT 1 FROM page_members pm
                    INNER JOIN users u ON u.id = pm.user_id
                    WHERE pm.page_id = pages.id
                    AND pm.status = 'approved'
                    AND u.faculty_id = ?
                ) THEN 1 ELSE 0 END DESC
            ", [$user->faculty_id]);
        }

        return $query->withCount('followers')
            ->orderByDesc('followers_count')
            ->limit($limit)
            ->get();
    }

    /**
     * Recommend posts based on pages followed and faculty.
     */
    public function recommendPosts(User $user, int $limit = 20): Collection
    {
        $followedPageIds = $user->followedPages()->pluck('pages.id');

        // Get page IDs of same faculty
        $facultyPageIds = $user->faculty_id
            ? Page::whereHas('members', fn ($q) =>
                $q->whereHas('user', fn ($u) => $u->where('faculty_id', $user->faculty_id))
              )->pluck('id')
            : collect();

        $allPageIds = $followedPageIds->merge($facultyPageIds)->unique();

        return Post::with(['author', 'page'])
            ->public()
            ->published()
            ->whereIn('page_id', $allPageIds)
            ->withCount('likes')
            ->orderByDesc('likes_count')
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }

    /**
     * Recommend events the user might be interested in.
     */
    public function recommendEvents(User $user, int $limit = 5): Collection
    {
        $participatedEventIds = $user->events()->pluck('events.id');

        return \App\Models\Event::whereHas('page', fn ($q) =>
                $q->whereIn('id', $user->followedPages()->pluck('pages.id'))
            )
            ->whereNotIn('id', $participatedEventIds)
            ->where('start_time', '>', now())
            ->orderBy('start_time')
            ->limit($limit)
            ->get();
    }
}