<?php

namespace App\Modules\Post\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Post\Services\PostService;
use Illuminate\Http\Request;

class FeedController extends Controller
{
    public function __construct(private PostService $postService) {}

    public function index(Request $request)
    {
        $user          = $request->user();
        $posts         = $this->postService->getFeedForUser($user);
        $followedPages = $user->followedPages()->withCount('followers')->latest()->get();

        return view('pages.dashboard', compact('posts', 'followedPages'));
    }
}