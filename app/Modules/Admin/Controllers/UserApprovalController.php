<?php

namespace App\Modules\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserApprovalController extends Controller
{
    public function index(Request $request)
    {
        $status       = $request->input('status', 'pending');
        $requestedRole = $request->input('requested_role');

        $query = User::with(['faculty', 'roleApprover'])
            ->when($status === 'pending',  fn($q) => $q->where('account_status', 'pending_upgrade'))
            ->when($status === 'approved', fn($q) => $q->where('account_status', 'active')
                                                        ->whereNotNull('role_approved_at'))
            ->when($status === 'rejected', fn($q) => $q->whereNotNull('role_reject_reason')
                                                        ->where('account_status', 'active'))
            ->when($requestedRole, fn($q) => $q->where('requested_role', $requestedRole))
            ->whereNotNull('requested_role')
            ->latest('role_requested_at');

        $users = $query->paginate(20);

        $counts = [
            'pending'  => User::where('account_status','pending_upgrade')->count(),
            'approved' => User::where('account_status','active')->whereNotNull('role_approved_at')->count(),
            'rejected' => User::whereNotNull('role_reject_reason')->where('account_status','active')->count(),
        ];

        return view('admin.users.approval', compact('users','status','counts','requestedRole'));
    }

    public function approve(Request $request, User $user)
    {
        $user->update([
            'role'             => 'page_admin',
            'account_status'   => 'active',
            'role_approved_at' => now(),
            'role_approved_by' => $request->user()->id,
            'role_reject_reason' => null,
        ]);

        return back()->with('success', "✅ Đã duyệt tài khoản \"{$user->display_name}\" lên vai trò Quản trị trang.");
    }

    public function reject(Request $request, User $user)
    {
        $request->validate([
            'reject_reason' => ['required', 'string', 'min:10', 'max:500'],
        ]);

        $user->update([
            'account_status'    => 'active',
            'role_reject_reason'=> $request->reject_reason,
            'role_approved_by'  => $request->user()->id,
            // Reset request fields so they can re-apply
            'requested_role'    => null,
            'role_requested_at' => null,
        ]);

        return back()->with('success', "❌ Đã từ chối yêu cầu của \"{$user->display_name}\".");
    }

    public function suspend(Request $request, User $user)
    {
        $user->update(['account_status' => 'suspended']);
        return back()->with('success', "Đã đình chỉ tài khoản {$user->display_name}.");
    }

    public function unsuspend(Request $request, User $user)
    {
        $user->update(['account_status' => 'active']);
        return back()->with('success', "Đã khôi phục tài khoản {$user->display_name}.");
    }
}