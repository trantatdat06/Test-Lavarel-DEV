<?php

namespace App\Modules\Auth\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Auth\Requests\RegisterRequest;
use App\Modules\Auth\Requests\LoginRequest;
use App\Modules\Auth\Requests\SendOtpRequest;
use App\Modules\Auth\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function __construct(private AuthService $authService) {}

    // ── Views ──────────────────────────────────────────────
    public function showLogin()
    {
        return view('auth.login');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    // ── Actions ────────────────────────────────────────────
    public function sendOtp(SendOtpRequest $request)
    {
        $this->authService->sendOtp($request->email);

        return back()->with('success', 'Mã OTP đã được gửi đến email của bạn.');
    }

    public function register(RegisterRequest $request)
    {
        $user = $this->authService->register($request->validated());

        Auth::login($user);

        return redirect()->route('onboarding.index');
    }

    public function login(LoginRequest $request)
    {
        if (!Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            return back()->withErrors(['email' => 'Email hoặc mật khẩu không đúng.']);
        }

        $request->session()->regenerate();

        $user = Auth::user();

        if ($user->first_login) {
            return redirect()->route('onboarding.index');
        }

        return redirect()->intended(route('feed.index'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('explore.index');
    }
}