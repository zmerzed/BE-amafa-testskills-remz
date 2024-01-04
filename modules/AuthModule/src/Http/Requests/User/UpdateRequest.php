<?php

namespace Boilerplate\Auth\Http\Requests\User;

use Boilerplate\Auth\Rules\ValidPhoneNumber;
use Boilerplate\Auth\Rules\ValidUser;
use Boilerplate\Auth\Support\ValidatesPhone;
use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    use ValidatesPhone;

    public function rules(): array
    {
        if ($this->route('user')) {
            $userId = $this->route('user')->id;
        } else {
            $userId = $this->route('id');
        }

        return [
            'first_name' => [
                'required',
                'string',
                'max:255',
                new ValidUser($userId)
            ],
            'last_name' => [
                'required',
                'string',
                'max:255',
                new ValidUser($userId)
            ],
            'phone_number' => [
                'sometimes',
                'required',
                new ValidPhoneNumber,
                "unique:users,phone_number,{$userId},id",
                new ValidUser($userId),
            ],
            'email' => [
                'sometimes',
                'required',
                'email',
                "unique:users,email,{$userId},id",
                new ValidUser($userId),
            ],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->get('phone_number')) {
            $this->merge([
                'phone_number' => $this->cleanPhoneNumber($this->get('phone_number')),
            ]);
        }
    }

    public function messages(): array
    {
        return [
            'first_name.sometimes' => 'First Name must not be empty.',
            'first_name.required' => 'First Name must not be empty.',
            'first_name.string' => 'First Name must not be empty.',
            'last_name.sometimes' => 'Last Name must not be empty.',
            'last_name.required' => 'Last Name must not be empty.',
            'last_name.string' => 'Last Name must not be empty.',
        ];
    }
}
