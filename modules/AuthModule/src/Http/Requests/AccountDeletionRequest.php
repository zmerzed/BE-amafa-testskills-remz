<?php

namespace Boilerplate\Auth\Http\Requests;

use Boilerplate\Auth\Rules\UsernameExist;
use Illuminate\Foundation\Http\FormRequest;

class AccountDeletionRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'username' => ['required', new UsernameExist()],
            'message' => 'required|string',
        ];
    }

    /**
     * Get the validation messages that apply to the request.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'email_address.exists' => 'Email address does not exist.',
        ];
    }
}
