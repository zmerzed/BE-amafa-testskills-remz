<?php

namespace Boilerplate\Auth\Http\Requests;

use Boilerplate\Auth\Rules\UsernameExist;
use Boilerplate\Auth\Rules\ValidEmailOrPhoneNumber;
use Illuminate\Foundation\Http\FormRequest;

class ForgotPasswordRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'username' => ['required', 'bail', new ValidEmailOrPhoneNumber, 'bail', new UsernameExist],
        ];
    }
}
