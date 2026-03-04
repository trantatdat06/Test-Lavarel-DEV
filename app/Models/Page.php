<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Page extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'parent_id','name','slug','description',
        'avatar','cover','email','phone','address',
        'type','status','category',
        'reject_reason','approved_at','approved_by','created_by',
    ];

    protected $casts = ['approved_at' => 'datetime'];

    const CATEGORIES = [
        'student_union' => 'Hội Sinh viên',
        'youth_union'   => 'Đoàn Thanh niên',
        'club'          => 'Câu lạc bộ',
        'faculty'       => 'Khoa / Bộ môn',
        'department'    => 'Phòng ban',
        'other'         => 'Khác',
    ];
    const CATEGORY_COLORS = [
        'student_union'=>'#16a34a','youth_union'=>'#dc2626',
        'club'=>'#7c3aed','faculty'=>'#1a2f4e','department'=>'#0891b2','other'=>'#6b7280',
    ];
    const CATEGORY_ICONS = [
        'student_union'=>'fa-users','youth_union'=>'fa-flag',
        'club'=>'fa-star','faculty'=>'fa-building-columns','department'=>'fa-building','other'=>'fa-globe',
    ];

    public function getCategoryLabelAttribute(): string  { return self::CATEGORIES[$this->category] ?? 'Khác'; }
    public function getCategoryColorAttribute(): string  { return self::CATEGORY_COLORS[$this->category] ?? '#6b7280'; }
    public function getCategoryIconAttribute(): string   { return self::CATEGORY_ICONS[$this->category] ?? 'fa-globe'; }
    public function isApproved(): bool { return $this->status === 'approved'; }
    public function isPending(): bool  { return $this->status === 'pending'; }
    public function isRejected(): bool { return $this->status === 'rejected'; }

    public function parent()          { return $this->belongsTo(Page::class, 'parent_id'); }
    public function children()        { return $this->hasMany(Page::class, 'parent_id'); }
    public function creator()         { return $this->belongsTo(User::class, 'created_by'); }
    public function approver()        { return $this->belongsTo(User::class, 'approved_by'); }
    public function members()         { return $this->hasMany(PageMember::class); }
    public function approvedMembers() { return $this->hasMany(PageMember::class)->where('status','approved'); }
    public function followers()       { return $this->belongsToMany(User::class,'page_follows')->withTimestamps(); }
    public function posts()           { return $this->hasMany(Post::class); }
    public function events()          { return $this->hasMany(Event::class); }
}