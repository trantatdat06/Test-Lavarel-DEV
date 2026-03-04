<?php

namespace App\Modules\Post\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Post\Services\PostService;
use Illuminate\Http\Request;

class ExploreController extends Controller
{
    public function __construct(private PostService $postService) {}

    public function index(Request $request)
    {
        $posts = $this->postService->getExplore();

        return view('pages.explore', compact('posts'));
    }
}