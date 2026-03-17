<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminController extends Controller
{
    /**
     * Hiển thị danh sách chờ duyệt
     */
    public function index()
    {
        $pendingPages = DB::table('pages')
            ->join('users', 'pages.created_by', '=', 'users.id')
            ->where('pages.status', 'pending')
            ->select('pages.*', 'users.full_name', 'users.email')
            ->get();
            
        return view('page.admin.admin', compact('pendingPages'));
    }

    /**
     * Duyệt yêu cầu: Hoàn trả lại 3 lượt
     */
    public function approvePage($id)
    {
        $page = DB::table('pages')->where('id', $id)->first();
        if (!$page) return redirect()->back();

        // 1. Duyệt Page
        DB::table('pages')->where('id', $id)->update([
            'status' => 'approved', 'approved_at' => now(), 'approved_by' => 1, 'updated_at' => now()
        ]);

        // 2. Duyệt Member
        DB::table('page_members')->where('page_id', $id)->where('user_id', $page->created_by)->update([
            'status' => 'approved', 'updated_at' => now()
        ]);

        // 3. HOÀN LẠI 3 LƯỢT CHO NGƯỜI DÙNG
        DB::table('users')->where('id', $page->created_by)->update(['upgrade_attempt_count' => 3]);

        return redirect()->back()->with('success', 'Đã duyệt và hoàn lại 3 lượt yêu cầu.');
    }

    /**
     * Từ chối yêu cầu: Trừ đi 1 lượt
     */
    public function rejectPage(Request $request, $id)
    {
        $page = DB::table('pages')->where('id', $id)->first();
        if (!$page) return redirect()->back();

        // 1. Từ chối Page
        DB::table('pages')->where('id', $id)->update([
            'status' => 'rejected', 'reject_reason' => $request->input('reject_reason'), 'updated_at' => now()
        ]);

        // 2. Từ chối Member
        DB::table('page_members')->where('page_id', $id)->where('user_id', $page->created_by)->update([
            'status' => 'rejected', 'updated_at' => now()
        ]);

        // 3. TRỪ ĐI 1 LƯỢT YÊU CẦU
        DB::table('users')->where('id', $page->created_by)->decrement('upgrade_attempt_count');

        return redirect()->back()->with('error', 'Đã từ chối và trừ 1 lượt yêu cầu.');
    }
}