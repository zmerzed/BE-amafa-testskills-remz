<?php

namespace Boilerplate\Auth\Support;

trait BypassCodeValidator
{
    public function isUsingBypassCode(?string $code, int $digits = 5): bool
    {
        return config('Boilerplate.auth.use_bypass_code') === true && $code === str_pad('0', $digits, '0');
    }
}
