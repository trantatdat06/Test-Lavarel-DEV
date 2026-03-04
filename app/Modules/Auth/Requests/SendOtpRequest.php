<?php

namespace App\Modules\Auth\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SendOtpRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'email' => ['required', 'email', 'ends_with:@hvnh.edu.vn'],
        ];
    }
}