<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PageMember extends Model
{
    protected $fillable = ['page_id', 'user_id', 'role', 'status'];

    public function page()
    {
        return $this->belongsTo(Page::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function canManageContent(): bool
    {
        return in_array($this->role, ['admin', 'content_manager']) && $this->isApproved();
    }

    public function canManageMembers(): bool
    {
        return in_array($this->role, ['admin', 'member_manager']) && $this->isApproved();
    }

    public function canManageInfo(): bool
    {
        return in_array($this->role, ['admin', 'info_manager']) && $this->isApproved();
    }
}