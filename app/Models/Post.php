<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Post extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'page_id', 'user_id', 'parent_post_id',
        'title', 'content', 'media_path', 'external_link',
        'visibility', 'tags', 'post_type', 'scheduled_at',
    ];

    protected $casts = [
        'tags' => 'array',
        'scheduled_at' => 'datetime',
    ];

    // ── Relationships ──────────────────────────────────────
    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function page()
    {
        return $this->belongsTo(Page::class);
    }

    public function originalPost()
    {
        return $this->belongsTo(Post::class, 'parent_post_id');
    }

    public function reposts()
    {
        return $this->hasMany(Post::class, 'parent_post_id');
    }

    public function likes()
    {
        return $this->belongsToMany(User::class, 'post_likes')
                    ->withTimestamps();
    }

    public function comments()
    {
        return $this->hasMany(PostComment::class);
    }

    public function saves()
    {
        return $this->belongsToMany(User::class, 'post_saves')
                    ->withTimestamps();
    }

    public function events()
    {
        return $this->hasMany(Event::class);
    }

    // ── Scopes ─────────────────────────────────────────────
    public function scopePublic($query)
    {
        return $query->where('visibility', 'public');
    }

    public function scopePublished($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('scheduled_at')
              ->orWhere('scheduled_at', '<=', now());
        });
    }

    public function scopeForFeed($query, User $user)
    {
        $followedPageIds = $user->followedPages()->pluck('pages.id');
        return $query->whereIn('page_id', $followedPageIds)
                     ->orWhere('user_id', $user->id);
    }

    // ── Helpers ────────────────────────────────────────────
    public function isRepost(): bool
    {
        return !is_null($this->parent_post_id);
    }

    public function isOriginalDeleted(): bool
    {
        return $this->isRepost() && is_null($this->originalPost);
    }

    public function isLikedBy(User $user): bool
    {
        return $this->likes()->where('user_id', $user->id)->exists();
    }

    public function isSavedBy(User $user): bool
    {
        return $this->saves()->where('user_id', $user->id)->exists();
    }
}