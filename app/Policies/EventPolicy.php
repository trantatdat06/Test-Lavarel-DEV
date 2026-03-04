<?php

namespace App\Policies;

use App\Models\Event;
use App\Models\User;

class EventPolicy
{
    public function viewAny(?User $user): bool { return true; }

    public function view(?User $user, Event $event): bool { return true; }

    public function create(User $user): bool { return true; }

    public function update(User $user, Event $event): bool
    {
        if ($user->isSuperAdmin()) return true;
        if (!$event->page_id) return false;

        $member = $event->page->members()
                        ->where('user_id', $user->id)
                        ->where('status', 'approved')
                        ->whereIn('role', ['admin', 'content_manager'])
                        ->first();
        return (bool) $member;
    }

    public function delete(User $user, Event $event): bool
    {
        return $this->update($user, $event);
    }
}