<?php

namespace App\Modules\User\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;

class OnboardingController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if (!$user->first_login) {
            return redirect()->route('feed.index');
        }

        $currentStep    = (int) $request->input('step', 1);
        $suggestedPages = Page::where('type', 'public')->withCount('followers')->orderByDesc('followers_count')->limit(10)->get();
        $faculties      = \App\Models\Faculty::orderBy('name')->get();

        return view('pages.onboarding', compact('currentStep', 'suggestedPages', 'faculties'));
    }

    public function store(Request $request)
    {
        $user = $request->user();
        $step = (int) $request->input('step', 1);

        match ($step) {
            1 => $this->handleStep1($request, $user),
            2 => $this->handleStep2($request, $user),
            3 => $this->handleStep3($request, $user),
        };

        if ($step >= 3) {
            $user->update(['first_login' => false]);
            return redirect()->route('feed.index')->with('success', 'Chào mừng bạn đến EDU Social! 🎉');
        }

        return redirect()->route('onboarding.index', ['step' => $step + 1]);
    }

    private function handleStep1(Request $request, $user): void
    {
        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->update(['avatar' => $path]);
        }
    }

    private function handleStep2(Request $request, $user): void
    {
        $data = $request->validate([
            'full_name'  => ['required', 'string', 'max:100'],
            'faculty_id' => ['nullable', 'exists:faculties,id'],
        ]);
        $user->update($data);
    }

    private function handleStep3(Request $request, $user): void
    {
        $pageIds = $request->input('follow_pages', []);
        if (!empty($pageIds)) {
            $user->followedPages()->syncWithoutDetaching($pageIds);
        }
    }
}