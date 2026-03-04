<?php

namespace App\Modules\Search\Services;

use App\Models\Page;
use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Collection;

class SearchService
{
    /**
     * Unified search across posts, pages, and users.
     */
    public function search(string $keyword, array $filters = []): array
    {
        return [
            'posts' => $this->searchPosts($keyword, $filters),
            'pages' => $this->searchPages($keyword),
            'users' => $this->searchUsers($keyword),
        ];
    }

    public function searchPosts(string $keyword, array $filters = [])
    {
        $query = Post::with(['author', 'page'])
            ->public()
            ->published()
            ->where(function ($q) use ($keyword) {
                $q->where('title', 'like', "%{$keyword}%")
                  ->orWhere('content', 'like', "%{$keyword}%");
            });

        // Filter by tag
        if (!empty($filters['tag'])) {
            $query->whereJsonContains('tags', $filters['tag']);
        }

        // Filter by post_type
        if (!empty($filters['type'])) {
            $query->where('post_type', $filters['type']);
        }

        // Filter by date range
        if (!empty($filters['from'])) {
            $query->whereDate('created_at', '>=', $filters['from']);
        }
        if (!empty($filters['to'])) {
            $query->whereDate('created_at', '<=', $filters['to']);
        }

        // Sort by interactions
        if (!empty($filters['sort']) && $filters['sort'] === 'popular') {
            $query->withCount('likes')->orderByDesc('likes_count');
        } else {
            $query->latest();
        }

        return $query->paginate(10, ['*'], 'posts_page');
    }

    public function searchPages(string $keyword)
    {
        return Page::where('type', 'public')
            ->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                  ->orWhere('description', 'like', "%{$keyword}%");
            })
            ->paginate(10, ['*'], 'pages_page');
    }

    public function searchUsers(string $keyword)
    {
        return User::where(function ($q) use ($keyword) {
            $q->where('full_name', 'like', "%{$keyword}%")
              ->orWhere('display_name', 'like', "%{$keyword}%")
              ->orWhere('student_code', 'like', "%{$keyword}%");
        })->paginate(10, ['*'], 'users_page');
    }
}