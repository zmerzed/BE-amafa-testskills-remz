<?php

namespace Boilerplate\Auth\Http\Requests;

use Boilerplate\Auth\Support\ValidatesPhone;
use Boilerplate\Media\Rules\UnassignedMedia;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    use ValidatesPhone;

    public function rules(): array
    {
        $userId = $this->user()->id;

        return [
            'first_name' => ['sometimes', 'string', 'max:255'],
            'last_name' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'birthdate' => ['nullable', 'date'],
            'gender' => ['nullable', 'in:male,female'],
            'avatar' => ['nullable', new UnassignedMedia],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->phone_number) {
            $this->merge([
                'phone_number' => $this->cleanPhoneNumber($this->phone_number),
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
