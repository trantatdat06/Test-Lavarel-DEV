<?php

namespace App\Modules\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserAdminController extends Controller
{
    public function index()
    {
        $users = User::withTrashed()->latest()->paginate(30);
        return view('admin.users.index', compact('users'));
    }

    public function show(User $user)
    {
        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'role'       => ['required', 'in:super_admin,user,page_admin'],
            'full_name'  => ['required', 'string', 'max:100'],
            'faculty_id' => ['nullable', 'exists:faculties,id'],
        ]);

        $user->update($data);

        return redirect()->route('admin.users.index')->with('success', 'Đã cập nhật người dùng.');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'Đã khóa tài khoản.');
    }

    public function create() { return view('admin.users.create'); }

    public function store(Request $request)
    {
        // Admin tạo user thủ công nếu cần
        return redirect()->route('admin.users.index');
    }
}