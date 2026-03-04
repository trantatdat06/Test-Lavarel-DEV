<?php

namespace App\Policies;

use App\Models\Form;
use App\Models\User;

class FormPolicy
{
    public function view(?User $user, Form $form): bool { return true; }

    public function create(User $user): bool { return true; }

    public function update(User $user, Form $form): bool
    {
        if ($user->isSuperAdmin()) return true;
        if (!$form->event?->page_id) return false;

        $member = $form->event->page->members()
                        ->where('user_id', $user->id)
                        ->where('status', 'approved')
                        ->whereIn('role', ['admin', 'content_manager'])
                        ->first();
        return (bool) $member;
    }

    public function delete(User $user, Form $form): bool
    {
        return $this->update($user, $form);
    }

    public function submit(User $user, Form $form): bool
    {
        return $form->is_active && $form->event?->isFormOpen();
    }
}