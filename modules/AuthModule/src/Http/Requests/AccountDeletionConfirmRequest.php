<?php

namespace Boilerplate\Auth\Http\Requests;

use Boilerplate\Auth\Rules\UsernameExist;
use Illuminate\Foundation\Http\FormRequest;

class AccountDeletionConfirmRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'phone_number' => 'required',
            'otp' => 'required',
        ];
    }
}
