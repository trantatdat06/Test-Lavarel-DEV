<?php

namespace App\Modules\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\Post;
use App\Models\User;

class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'users'  => User::count(),
            'pages'  => Page::count(),
            'posts'  => Post::count(),
            'today'  => User::whereDate('created_at', today())->count(),
        ];

        return view('admin.dashboard', compact('stats'));
    }
}