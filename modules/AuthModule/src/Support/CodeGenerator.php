<?php

namespace Boilerplate\Auth\Support;

class CodeGenerator
{
    public static function make(int $digits = 5): string
    {
        return str_pad(rand(0, pow(10, $digits) - 1), $digits, '0', STR_PAD_LEFT);
    }
}
