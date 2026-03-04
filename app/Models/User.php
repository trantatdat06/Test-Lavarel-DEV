<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'student_code','email','password','full_name','display_name',
        'phone','faculty_id','class_id','role','account_status',
        'requested_role','role_request_reason','role_request_evidence',
        'role_requested_at','role_approved_at','role_approved_by','role_reject_reason',
        'upgrade_attempt_count','upgrade_locked_at','first_login',
        'avatar','cover','bio','website','email_verified_at',
    ];

    protected $hidden = ['password','remember_token'];

    protected $casts = [
        'email_verified_at'  => 'datetime',
        'upgrade_locked_at'  => 'datetime',
        'role_requested_at'  => 'datetime',
        'role_approved_at'   => 'datetime',
        'first_login'        => 'boolean',
        'password'           => 'hashed',
    ];

    // ── Role request labels ────────────────────────────────
    const REQUESTED_ROLES = [
        'student_union'    => 'Hội Sinh viên',
        'youth_union'      => 'Đoàn Thanh niên',
        'club_admin'       => 'Ban quản trị CLB',
        'faculty_staff'    => 'Cán bộ Khoa',
        'department_staff' => 'Nhân viên Phòng ban',
    ];

    const REQUESTED_ROLE_ICONS = [
        'student_union'    => 'fa-users',
        'youth_union'      => 'fa-flag',
        'club_admin'       => 'fa-star',
        'faculty_staff'    => 'fa-building-columns',
        'department_staff' => 'fa-building',
    ];

    const REQUESTED_ROLE_COLORS = [
        'student_union'    => '#16a34a',
        'youth_union'      => '#dc2626',
        'club_admin'       => '#7c3aed',
        'faculty_staff'    => '#1a2f4e',
        'department_staff' => '#0891b2',
    ];

    public function getRequestedRoleLabelAttribute(): ?string
    {
        return $this->requested_role ? (self::REQUESTED_ROLES[$this->requested_role] ?? null) : null;
    }
    public function getRequestedRoleColorAttribute(): string
    {
        return self::REQUESTED_ROLE_COLORS[$this->requested_role] ?? '#6b7280';
    }
    public function getRequestedRoleIconAttribute(): string
    {
        return self::REQUESTED_ROLE_ICONS[$this->requested_role] ?? 'fa-user';
    }

    // ── Helpers ────────────────────────────────────────────
    public function isSuperAdmin(): bool { return $this->role === 'super_admin'; }
    public function isPageAdmin(): bool  { return in_array($this->role, ['page_admin','super_admin']); }
    public function isPendingUpgrade(): bool { return $this->account_status === 'pending_upgrade'; }
    public function isSuspended(): bool  { return $this->account_status === 'suspended'; }

    // ── Relationships ──────────────────────────────────────
    public function faculty()         { return $this->belongsTo(Faculty::class); }
    public function posts()           { return $this->hasMany(Post::class); }
    public function ownedPages()      { return $this->hasMany(Page::class, 'created_by'); }
    public function pageMembers()     { return $this->hasMany(PageMember::class); }
    public function followedPages()   { return $this->belongsToMany(Page::class,'page_follows')->withTimestamps(); }
    public function likedPosts()      { return $this->belongsToMany(Post::class,'post_likes')->withTimestamps(); }
    public function savedPosts()      { return $this->belongsToMany(Post::class,'post_saves')->withTimestamps(); }
    public function comments()        { return $this->hasMany(PostComment::class); }
    public function events()          { return $this->belongsToMany(Event::class,'event_participants')->withPivot('status')->withTimestamps(); }
    public function roleApprover()    { return $this->belongsTo(User::class,'role_approved_by'); }
    public function notifications()   { return $this->hasMany(Notification::class,'user_id')->orderByDesc('created_at'); }
    public function unreadNotificationsCount(): int { return $this->notifications()->whereNull('read_at')->count(); }

    // ── Accessors ──────────────────────────────────────────
    public function getDisplayNameAttribute($value): string
    {
        return $value ?? $this->full_name;
    }
    public function getAvatarUrlAttribute(): string
    {
        return $this->avatar
            ? asset('storage/'.$this->avatar)
            : 'https://ui-avatars.com/api/?name='.urlencode($this->full_name).'&background=1a2f4e&color=fff';
    }
}