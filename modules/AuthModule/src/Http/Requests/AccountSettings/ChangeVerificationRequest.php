<?php

namespace Boilerplate\Auth\Http\Requests\AccountSettings;

use Illuminate\Foundation\Http\FormRequest;

class ChangeVerificationRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'token' => [
                'required',
            ],
        ];
    }
}
