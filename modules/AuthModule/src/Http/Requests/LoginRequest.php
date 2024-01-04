<?php

namespace Boilerplate\Auth\Http\Requests;

use Boilerplate\Auth\Rules\ValidEmailOrPhoneNumber;
use Boilerplate\Auth\Support\ValidatesEmail;
use Boilerplate\Auth\Support\ValidatesPhone;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LoginRequest extends FormRequest
{
    use ValidatesPhone;
    use ValidatesEmail;

    public function rules(): array
    {
        return [
            'username' => ['required', new ValidEmailOrPhoneNumber],
            'password' => [
                'bail',
                Rule::requiredIf($this->isEmail($this->get('username'))),
            ],
            'otp' => [
                'bail',
                Rule::requiredIf(
                    $this->isPhone($this->get('username')) &&
                    !config('Boilerplate.auth.use_bypass_code')
                ),
            ],
        ];
    }
}
