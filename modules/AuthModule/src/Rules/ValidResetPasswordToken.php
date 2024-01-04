<?php

namespace Boilerplate\Auth\Rules;

use Boilerplate\Auth\Models\User;
use Boilerplate\Auth\Support\BypassCodeValidator;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Carbon;

class ValidResetPasswordToken implements Rule
{
    use BypassCodeValidator;

    private string $message;

    public function __construct(private string $username)
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        $user = User::hasUsername($this->username)->first();

        if (!$user) {
            return false;
        }

        $pr = $user->passwordReset;

        if ($this->isUsingBypassCode($value)) {
            return true;
        }

        if ($pr && $pr->token == $value && $pr->expires_at->greaterThan(Carbon::now())) {
            return true;
        }

        return false;
    }

    public function message(): string
    {
        return trans('passwords.token');
    }
}
