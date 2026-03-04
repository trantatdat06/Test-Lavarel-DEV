<?php

namespace App\Policies;

use App\Models\Page;
use App\Models\User;

class PagePolicy
{
    public function viewAny(?User $user): bool
    {
        return true; // Public pages visible to guests
    }

    public function view(?User $user, Page $page): bool
    {
        if ($page->isPublic()) return true;
        return $user && ($page->hasMember($user) || $page->followers->contains($user));
    }

    public function create(User $user): bool
    {
        return $user->isPageAdmin();
    }

    public function update(User $user, Page $page): bool
    {
        // Must be an approved member with info_manager or admin role
        // Parent page does NOT have implicit rights over child page
        $member = $page->members()
                       ->where('user_id', $user->id)
                       ->where('status', 'approved')
                       ->first();

        if (!$member) return false;

        return in_array($member->role, ['admin', 'info_manager']);
    }

    public function delete(User $user, Page $page): bool
    {
        if ($user->isSuperAdmin()) return true;
        return $page->created_by === $user->id;
    }

    public function manageMembers(User $user, Page $page): bool
    {
        $member = $page->members()
                       ->where('user_id', $user->id)
                       ->where('status', 'approved')
                       ->first();

        return $member && in_array($member->role, ['admin', 'member_manager']);
    }

    public function createPost(User $user, Page $page): bool
    {
        $member = $page->members()
                       ->where('user_id', $user->id)
                       ->where('status', 'approved')
                       ->first();

        return $member && in_array($member->role, ['admin', 'content_manager']);
    }
}