<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Event extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'page_id', 'post_id', 'title', 'description',
        'location', 'start_time', 'end_time',
        'form_open_at', 'form_close_at',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'form_open_at' => 'datetime',
        'form_close_at' => 'datetime',
    ];

    public function page()          { return $this->belongsTo(Page::class); }
    public function post()          { return $this->belongsTo(Post::class); }
    public function forms()         { return $this->hasMany(Form::class); }
    public function participants()  { return $this->belongsToMany(User::class, 'event_participants')->withPivot('status')->withTimestamps(); }

    public function isFormOpen(): bool
    {
        $now = now();
        return (!$this->form_open_at || $now->gte($this->form_open_at))
            && (!$this->form_close_at || $now->lte($this->form_close_at));
    }
}