<?php

namespace App\Modules\Auth\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'email'     => ['required', 'email', 'ends_with:@hvnh.edu.vn', 'unique:users,email'],
            'otp'       => ['required', 'digits:6'],
            'full_name' => ['required', 'string', 'max:100'],
            'password'  => [
                'required',
                'confirmed',
                Password::min(11)->mixedCase()->symbols(),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'password.min' => 'Mật khẩu phải có ít nhất 11 ký tự.',
            'password.symbols' => 'Mật khẩu phải chứa ít nhất 1 ký tự đặc biệt.',
        ];
    }
}