<?php

namespace App\Modules\User\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function show(string $studentCode, Request $request)
    {
        $profileUser = User::where('student_code', strtoupper($studentCode))->firstOrFail();

        $type  = $request->input('type');
        $posts = $profileUser->posts()
            ->with(['author', 'page'])
            ->when($type, fn($q) => $q->where('post_type', $type))
            ->public()
            ->published()
            ->latest()
            ->paginate(12);

        return view('pages.profile', compact('profileUser', 'posts'));
    }

    public function edit(Request $request)
    {
        return view('pages.profile-edit', ['user' => $request->user()]);
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'full_name'    => ['required', 'string', 'max:100'],
            'display_name' => ['nullable', 'string', 'max:100'],
            'bio'          => ['nullable', 'string', 'max:500'],
            'phone'        => ['nullable', 'string', 'max:20'],
            'website'      => ['nullable', 'url', 'max:255'],
            'avatar'       => ['nullable', 'image', 'max:5120'],
            'cover'        => ['nullable', 'image', 'max:10240'],
        ]);

        if ($request->hasFile('avatar')) {
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }
        if ($request->hasFile('cover')) {
            $data['cover'] = $request->file('cover')->store('covers', 'public');
        }

        unset($data['avatar_file'], $data['cover_file']);
        $user->update($data);

        return back()->with('success', 'Hồ sơ đã được cập nhật!');
    }

    public function schedule(string $studentCode)
    {
        $profileUser = User::where('student_code', strtoupper($studentCode))->firstOrFail();
        $events      = $profileUser->events()->orderBy('start_time')->get();

        return view('pages.profile-schedule', compact('profileUser', 'events'));
    }

    public function events(string $studentCode)
    {
        $profileUser = User::where('student_code', strtoupper($studentCode))->firstOrFail();
        $events      = $profileUser->events()->with('page')->orderByDesc('start_time')->paginate(10);

        return view('pages.profile-events', compact('profileUser', 'events'));
    }

    public function saved(string $studentCode, Request $request)
    {
        abort_unless(auth()->user()?->student_code === strtoupper($studentCode), 403);

        $profileUser = $request->user();
        $posts       = $profileUser->savedPosts()->with(['author', 'page'])->paginate(12);

        return view('pages.profile-saved', compact('profileUser', 'posts'));
    }

    public function roles(Request $request)
    {
        $user  = $request->user();
        $roles = $user->pageMembers()->with('page')->where('status', 'approved')->get();

        return view('pages.profile-roles', compact('user', 'roles'));
    }
}