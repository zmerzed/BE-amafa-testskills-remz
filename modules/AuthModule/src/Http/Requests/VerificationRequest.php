<?php

namespace Boilerplate\Auth\Http\Requests;

use Boilerplate\Auth\Enums\UsernameType;
use BenSampo\Enum\Rules\EnumValue;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class VerificationRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'token' => ['required', 'bail'],
            'via' => ['required', 'bail', new EnumValue(UsernameType::class)],
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
                    } else {
                        if (!$this->user()->isValidEmailVerificationCode($this->token)) {
                            $validator->errors()->add(
                                'token',
                                trans('auth::validation.verification.email_token_not_match')
                            );
                        }
                    }
                    break;

                case UsernameType::PHONE_NUMBER:
                    if (!$this->user()->hasPhoneNumber()) {
                        $validator->errors()->add(
                            'phone_number',
                            trans('auth::validation.verification.phone_number_not_found')
                        );
                    } else {
                        if (!$this->user()->isValidPhoneVerificationCode($this->token)) {
                            $validator->errors()->add(
                                'token',
                                trans('auth::validation.verification.phone_number_token_not_match')
                            );
                        }
                    }
                    break;
            }
        });
    }
}
