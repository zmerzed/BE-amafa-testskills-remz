<?php

namespace Boilerplate\Auth\Http\Requests;

use Boilerplate\Auth\Models\User;
use Boilerplate\Auth\Rules\UniquePhoneNumber;
use Boilerplate\Auth\Rules\ValidPhoneNumber;
use Boilerplate\Auth\Support\BypassCodeValidator;
use Boilerplate\Auth\Support\OneTimePassword\InteractsWithOneTimePassword;
use Boilerplate\Auth\Support\ValidatesPhone;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class RegisterUserRequest extends FormRequest
{
    use ValidatesPhone;
    use InteractsWithOneTimePassword;
    use BypassCodeValidator;

    protected $stopOnFirstFailure = false;

    public function rules(): array
    {
        return [
            'email' => [
                Rule::requiredIf(!$this->has('phone_number')),
                'bail',
                'email',
                'max:255',
                'unique:users',
            ],
            'phone_number' => [
                Rule::requiredIf(!$this->has('email')),
                'bail',
                'nullable',
                new ValidPhoneNumber,
                new UniquePhoneNumber(User::withBlocked()),
            ],
            'first_name' => [
                'sometimes',
                'required',
                'string',
                'max:255',
            ],
            'last_name' => [
                'sometimes',
                'required',
                'string',
                'max:255',
            ],
            'password' => [
                Rule::requiredIf($this->has('email')),
                'string',
                'min:8',
                'confirmed',
            ],
            'otp' => [
                Rule::requiredIf(
                    $this->has('phone_number') &&
                    filled($this->input('phone_number')) &&
                    !config('Boilerplate.auth.use_bypass_code')
                ),
                'string',
                'max:5',
            ],
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

    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            if ($this->has('otp')) {
                $otp = $this->get('otp');
                $phoneNumber = $this->uncleanPhoneNumber($this->get('phone_number'));
                if (!$this->isUsingBypassCode($otp) && !$this->hasValidOneTimePassword($phoneNumber, $otp)) {
                    $validator->errors()->add('otp', 'The one time password is invalid');
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'first_name.sometimes' => 'First Name must not be empty.',
            'first_name.required' => 'First Name must not be empty.',
            'first_name.string' => 'First Name must not be empty.',
            'last_name.sometimes' => 'Last Name must not be empty.',
            'last_name.required' => 'Last Name must not be empty.',
            'last_name.string' => 'Last Name must not be empty.'
        ];
    }
}
