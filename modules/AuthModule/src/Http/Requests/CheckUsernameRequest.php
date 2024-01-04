<?php

namespace Boilerplate\Auth\Http\Requests;

use Boilerplate\Auth\Enums\Role;
use Boilerplate\Auth\Rules\ValidEmailOrPhoneNumber;
use BenSampo\Enum\Rules\EnumValue;
use Illuminate\Foundation\Http\FormRequest;

class CheckUsernameRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'username' => ['required', new ValidEmailOrPhoneNumber],
            'role' => [
                'nullable',
                'string',
                new EnumValue(Role::class),
            ],
        ];
    }
}
