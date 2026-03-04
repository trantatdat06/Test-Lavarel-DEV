<?php

namespace App\Modules\Post\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SavedController extends Controller
{
    public function index(Request $request)
    {
        $posts = $request->user()
            ->savedPosts()
            ->with(['author', 'page'])
            ->latest('post_saves.created_at')
            ->paginate(15);

        return view('pages.saved', compact('posts'));
    }
}