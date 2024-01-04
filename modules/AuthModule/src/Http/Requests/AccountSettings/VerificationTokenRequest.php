<?php

namespace Boilerplate\Auth\Http\Requests\AccountSettings;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VerificationTokenRequest extends FormRequest
{
    public function rules(): array
    {
        $user = $this->user();

        return [
            'password' => Rule::requiredIf($user->isEmailPrimary()),
            'otp' => Rule::requiredIf($user->isPhonePrimary()),
        ];
    }
}
