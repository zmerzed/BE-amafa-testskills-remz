<?php

namespace Boilerplate\Auth\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAvatarRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'avatar' => 'required|image',
        ];
    }
}
