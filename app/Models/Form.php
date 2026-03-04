<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Form extends Model
{
    use SoftDeletes;

    protected $fillable = ['event_id', 'title', 'description', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function event()       { return $this->belongsTo(Event::class); }
    public function fields()      { return $this->hasMany(FormField::class)->orderBy('order'); }
    public function submissions() { return $this->hasMany(FormSubmission::class); }
}

class FormField extends Model
{
    protected $fillable = ['form_id', 'label', 'type', 'mapping_key', 'options', 'required', 'order'];

    protected $casts = [
        'options' => 'array',
        'required' => 'boolean',
    ];

    public function form() { return $this->belongsTo(Form::class); }
}

class FormSubmission extends Model
{
    use SoftDeletes;

    protected $fillable = ['form_id', 'user_id', 'data', 'status', 'submitted_at', 'note'];

    protected $casts = [
        'data' => 'array',
        'submitted_at' => 'datetime',
    ];

    public function form()   { return $this->belongsTo(Form::class); }
    public function user()   { return $this->belongsTo(User::class); }
}