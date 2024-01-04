<?php

namespace Boilerplate\Auth\Rules;

use Boilerplate\Auth\Support\ValidatesEmail;
use Boilerplate\Auth\Support\ValidatesPhone;
use Illuminate\Contracts\Validation\Rule;

class ValidEmailOrPhoneNumber implements Rule
{
    use ValidatesEmail;
    use ValidatesPhone;

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        if ($this->isEmail($value) || $this->isPhone($value)) {
            return true;
        }
        return false;
    }

    public function message(): string
    {
        return trans('auth::validation.valid_email_or_phone_number');
    }
}
