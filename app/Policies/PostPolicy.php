<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;

class PostPolicy
{
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, Post $post): bool
    {
        if ($post->visibility === 'public') return true;
        return $user && $post->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return true; // Any authenticated user can post
    }

    public function update(User $user, Post $post): bool
    {
        if ($user->isSuperAdmin()) return true;

        // Author can edit
        if ($post->user_id === $user->id) return true;

        // Page admin/content_manager can edit page posts
        if ($post->page_id) {
            $member = $post->page->members()
                           ->where('user_id', $user->id)
                           ->where('status', 'approved')
                           ->first();
            return $member && in_array($member->role, ['admin', 'content_manager']);
        }

        return false;
    }

    public function delete(User $user, Post $post): bool
    {
        if ($user->isSuperAdmin()) return true;
        if ($post->user_id === $user->id) return true;

        if ($post->page_id) {
            $member = $post->page->members()
                           ->where('user_id', $user->id)
                           ->where('status', 'approved')
                           ->first();
            return $member && $member->role === 'admin';
        }

        return false;
    }

    public function repost(User $user, Post $post): bool
    {
        return $post->visibility === 'public' && !$post->trashed();
    }
}