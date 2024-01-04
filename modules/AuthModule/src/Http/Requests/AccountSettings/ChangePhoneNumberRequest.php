<?php

namespace Boilerplate\Auth\Http\Requests\AccountSettings;

use Boilerplate\Auth\Models\User;
use Boilerplate\Auth\Rules\UniquePhoneNumber;
use Boilerplate\Auth\Rules\ValidPhoneNumber;
use Illuminate\Foundation\Http\FormRequest;

class ChangePhoneNumberRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'phone_number' => [
                'required',
                new ValidPhoneNumber,
                new UniquePhoneNumber(User::withBlocked()),
            ],
        ];
    }
}
