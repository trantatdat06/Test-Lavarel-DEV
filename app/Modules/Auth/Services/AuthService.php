<?php

namespace App\Modules\Auth\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\ValidationException;

class AuthService
{
    private const OTP_TTL_MINUTES = 10;
    private const EDU_DOMAIN = '@hvnh.edu.vn';

    /**
     * Send OTP to the given email.
     */
    public function sendOtp(string $email): void
    {
        $this->assertEduEmail($email);

        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        Cache::put("otp:{$email}", Hash::make($otp), now()->addMinutes(self::OTP_TTL_MINUTES));

        Mail::to($email)->send(new \App\Modules\Auth\Mail\OtpMail($otp));
    }

    /**
     * Verify OTP and register the user.
     */
    public function register(array $data): User
    {
        $this->assertEduEmail($data['email']);
        $this->verifyOtp($data['email'], $data['otp']);

        $studentCode = $this->extractStudentCode($data['email']);

        $user = User::create([
            'student_code' => $studentCode,
            'email'        => $data['email'],
            'password'     => $data['password'],
            'full_name'    => $data['full_name'],
            'display_name' => $data['full_name'],
            'role'         => 'user',
            'first_login'  => true,
            'email_verified_at' => now(),
        ]);

        Cache::forget("otp:{$data['email']}");

        return $user;
    }

    public function verifyOtp(string $email, string $otp): void
    {
        $cached = Cache::get("otp:{$email}");

        if (!$cached || !Hash::check($otp, $cached)) {
            throw ValidationException::withMessages([
                'otp' => 'Mã OTP không hợp lệ hoặc đã hết hạn.',
            ]);
        }
    }

    public function extractStudentCode(string $email): string
    {
        return strtoupper(explode('@', $email)[0]);
    }

    private function assertEduEmail(string $email): void
    {
        if (!str_ends_with(strtolower($email), self::EDU_DOMAIN)) {
            throw ValidationException::withMessages([
                'email' => 'Chỉ chấp nhận email ' . self::EDU_DOMAIN,
            ]);
        }
    }
}