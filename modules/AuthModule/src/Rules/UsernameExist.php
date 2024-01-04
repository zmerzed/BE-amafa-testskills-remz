<?php

namespace Boilerplate\Auth\Rules;

use Boilerplate\Auth\Models\User;
use Illuminate\Contracts\Validation\Rule;

class UsernameExist implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        $exist = User::hasUsername($value)->count();

        return !!$exist;
    }

    public function message(): string
    {
        return trans('auth::validation.username_not_found');
    }
}
