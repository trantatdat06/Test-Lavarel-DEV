<?php

namespace App\Modules\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;

class PageAdminController extends Controller
{
    public function index()
    {
        $pages = Page::withTrashed()->withCount('followers')->latest()->paginate(30);
        return view('admin.pages.index', compact('pages'));
    }

    public function show(Page $page)
    {
        return view('admin.pages.show', compact('page'));
    }

    public function edit(Page $page)
    {
        return view('admin.pages.edit', compact('page'));
    }

    public function update(Request $request, Page $page)
    {
        $page->update($request->only(['name', 'type', 'description']));
        return redirect()->route('admin.pages.index')->with('success', 'Đã cập nhật trang.');
    }

    public function destroy(Page $page)
    {
        $page->delete();
        return redirect()->route('admin.pages.index')->with('success', 'Đã xóa trang.');
    }

    public function create() { return view('admin.pages.create'); }
    public function store()  { return redirect()->route('admin.pages.index'); }
}