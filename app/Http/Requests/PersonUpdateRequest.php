<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class PersonUpdateRequest extends FormRequest
{

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255'
            ]
        ];
    }

    protected function prepareForValidation(): void
    {

    }

    public function withValidator(Validator $validator): void
    {
     
    }
}
