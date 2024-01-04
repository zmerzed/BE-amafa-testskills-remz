<?php

namespace Boilerplate\Auth\Http\Requests;

use Boilerplate\Auth\Enums\UsernameType;
use BenSampo\Enum\Rules\EnumValue;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class ResendVerificationRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'via' => ['required', new EnumValue(UsernameType::class)],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            switch ($this->via) {
                case UsernameType::EMAIL:
                    if (!$this->user()->hasEmail()) {
                        $validator->errors()->add(
                            'email',
                            trans('auth::validation.verification.email_not_found')
                        );
                    }
                    break;

                case UsernameType::PHONE_NUMBER:
                    if (!$this->user()->hasPhoneNumber()) {
                        $validator->errors()->add(
                            'phone_number',
                            trans('auth::validation.verification.phone_number_not_found')
                        );
                    }
                    break;
            }
        });
    }
}
