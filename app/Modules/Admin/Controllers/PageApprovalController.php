<?php

namespace App\Modules\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class PageApprovalController extends Controller
{
    public function index(Request $request)
    {
        $status   = $request->input('status', 'pending');
        $category = $request->input('category');

        $pages = Page::with(['creator', 'approver'])
            ->when($status, fn($q) => $q->where('status', $status))
            ->when($category, fn($q) => $q->where('category', $category))
            ->withCount('followers')
            ->latest()
            ->paginate(20);

        $counts = [
            'pending'  => Page::where('status', 'pending')->count(),
            'approved' => Page::where('status', 'approved')->count(),
            'rejected' => Page::where('status', 'rejected')->count(),
        ];

        return view('admin.pages.approval', compact('pages', 'status', 'counts', 'category'));
    }

    public function approve(Request $request, Page $page)
    {
        $page->update([
            'status'      => 'approved',
            'approved_at' => now(),
            'approved_by' => $request->user()->id,
            'reject_reason' => null,
        ]);

        return back()->with('success', "✅ Đã duyệt trang \"{$page->name}\"");
    }

    public function reject(Request $request, Page $page)
    {
        $request->validate([
            'reject_reason' => ['required', 'string', 'min:10', 'max:500'],
        ]);

        $page->update([
            'status'        => 'rejected',
            'reject_reason' => $request->reject_reason,
            'approved_by'   => $request->user()->id,
        ]);

        return back()->with('success', "❌ Đã từ chối trang \"{$page->name}\"");
    }

    public function bulkApprove(Request $request)
    {
        $ids = $request->input('ids', []);

        Page::whereIn('id', $ids)->where('status', 'pending')->update([
            'status'      => 'approved',
            'approved_at' => now(),
            'approved_by' => $request->user()->id,
        ]);

        return back()->with('success', '✅ Đã duyệt ' . count($ids) . ' trang');
    }

    public function detail(Page $page)
    {
        $page->load(['creator', 'approver', 'members.user']);
        return view('admin.pages.approval-detail', compact('page'));
    }
}