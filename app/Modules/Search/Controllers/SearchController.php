<?php

namespace App\Modules\Search\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Search\Services\SearchService;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function __construct(private SearchService $searchService) {}

    public function index(Request $request)
    {
        $keyword = trim($request->input('q', ''));

        if (!$keyword) {
            return view('pages.search', ['results' => null, 'keyword' => '']);
        }

        $filters = $request->only(['tag', 'type', 'from', 'to', 'sort']);
        $results = $this->searchService->search($keyword, $filters);

        return view('pages.search', compact('results', 'keyword'));
    }
}