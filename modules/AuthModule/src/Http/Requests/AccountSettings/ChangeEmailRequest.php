<?php

namespace Boilerplate\Auth\Http\Requests\AccountSettings;

use Illuminate\Foundation\Http\FormRequest;

class ChangeEmailRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users',
            ],
        ];
    }
}
