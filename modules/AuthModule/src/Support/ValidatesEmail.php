<?php

namespace Boilerplate\Auth\Support;

use Egulias\EmailValidator\EmailValidator;
use Egulias\EmailValidator\Validation\DNSCheckValidation;
use Egulias\EmailValidator\Validation\MultipleValidationWithAnd;
use Egulias\EmailValidator\Validation\RFCValidation;
use Egulias\EmailValidator\Validation\SpoofCheckValidation;

trait ValidatesEmail
{
    private function isEmail(?string $value): bool
    {
        if (!$value) {
            return false;
        }

        return (new EmailValidator())->isValid(
                $value,
                new MultipleValidationWithAnd(
                    array_filter([
                        new RFCValidation(),
                        app()->isProduction() ? new DNSCheckValidation() : null,
                        // new SpoofCheckValidation(),
                    ])
                )
            ) && filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
    }
}
