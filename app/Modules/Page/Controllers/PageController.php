<?php

namespace App\Modules\Page\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\PageMember;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PageController extends Controller
{
    public function all()
    {
        $pages = Page::where('type', 'public')
            ->withCount('followers')
            ->latest()
            ->paginate(20);

        return view('page.all', compact('pages'));
    }

    public function following(Request $request)
    {
        $pages = $request->user()->followedPages()->withCount('followers')->paginate(20);

        return view('page.following', compact('pages'));
    }

    public function show(Page $page)
    {
        $this->authorize('view', $page);

        $page->load(['posts' => fn($q) => $q->with(['author'])->published()->latest()->paginate(10)]);

        return view('page.show', compact('page'));
    }

    public function create()
    {
        $this->authorize('create', Page::class);
        return view('page.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', Page::class);

        $data = $request->validate([
            'name'        => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string'],
            'type'        => ['required', 'in:public,private'],
            'parent_id'   => ['nullable', 'exists:pages,id'],
            'email'       => ['nullable', 'email'],
            'phone'       => ['nullable', 'string'],
            'address'     => ['nullable', 'string'],
        ]);

        $data['slug']       = Str::slug($data['name']) . '-' . Str::random(4);
        $data['created_by'] = $request->user()->id;

        $page = Page::create($data);

        // Auto-add creator as admin
        PageMember::create([
            'page_id' => $page->id,
            'user_id' => $request->user()->id,
            'role'    => 'admin',
            'status'  => 'approved',
        ]);

        return redirect()->route('pages.show', $page)->with('success', 'Trang đã được tạo!');
    }

    public function update(Request $request, Page $page)
    {
        $this->authorize('update', $page);

        $page->update($request->only(['name', 'description', 'email', 'phone', 'address', 'type']));

        return back()->with('success', 'Đã cập nhật trang.');
    }

    public function destroy(Page $page)
    {
        $this->authorize('delete', $page);
        $page->delete();

        return redirect()->route('pages.all')->with('success', 'Đã xóa trang.');
    }

    public function toggleFollow(Request $request, Page $page)
    {
        $user      = $request->user();
        $following = $page->followers()->where('user_id', $user->id)->exists();

        if ($following) {
            $page->followers()->detach($user->id);
            $msg = 'Đã bỏ theo dõi trang.';
        } else {
            $page->followers()->attach($user->id);
            $msg = 'Đã theo dõi trang!';
        }

        return back()->with('success', $msg);
    }

    public function addMember(Request $request, Page $page)
    {
        $this->authorize('manageMembers', $page);

        $data = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'role'    => ['required', 'in:admin,content_manager,member_manager,info_manager,system_manager'],
        ]);

        PageMember::updateOrCreate(
            ['page_id' => $page->id, 'user_id' => $data['user_id']],
            ['role' => $data['role'], 'status' => 'approved']
        );

        return back()->with('success', 'Đã thêm thành viên.');
    }
} 