<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    // THÊM DÒNG NÀY VÀO ĐỂ CHO PHÉP THÊM TÊN TAG
    protected $fillable = ['name'];
}