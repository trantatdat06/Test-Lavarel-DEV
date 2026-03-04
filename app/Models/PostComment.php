<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PostComment extends Model
{
    use SoftDeletes;

    protected $fillable = ['post_id', 'user_id', 'parent_id', 'content'];

    public function post()     { return $this->belongsTo(Post::class); }
    public function author()   { return $this->belongsTo(User::class, 'user_id'); }
    public function parent()   { return $this->belongsTo(PostComment::class, 'parent_id'); }
    public function replies()  { return $this->hasMany(PostComment::class, 'parent_id'); }
}