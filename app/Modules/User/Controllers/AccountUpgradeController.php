<?php

namespace App\Modules\User\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AccountUpgradeController extends Controller
{
    /** Form xin nâng cấp tài khoản */
    public function create()
    {
        $roles = User::REQUESTED_ROLES;
        return view('user.upgrade-request', compact('roles'));
    }

    /** Gửi yêu cầu nâng cấp */
    public function store(Request $request)
    {
        $user = $request->user();

        if ($user->isPendingUpgrade()) {
            return back()->with('info', 'Yêu cầu của bạn đang chờ admin xét duyệt.');
        }

        $data = $request->validate([
            'requested_role'      => ['required', 'in:' . implode(',', array_keys(User::REQUESTED_ROLES))],
            'role_request_reason' => ['required', 'string', 'min:20', 'max:1000'],
            'evidence'            => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ]);

        $evidencePath = null;
        if ($request->hasFile('evidence')) {
            $evidencePath = $request->file('evidence')->store('role-evidence', 'public');
        }

        $user->update([
            'requested_role'       => $data['requested_role'],
            'role_request_reason'  => $data['role_request_reason'],
            'role_request_evidence'=> $evidencePath,
            'role_requested_at'    => now(),
            'account_status'       => 'pending_upgrade',
            'role_reject_reason'   => null,
        ]);

        return redirect()->route('feed.index')
            ->with('success', '✅ Yêu cầu nâng cấp tài khoản đã được gửi. Admin sẽ xét duyệt trong 1-3 ngày làm việc.');
    }
}