<?php

namespace Boilerplate\Auth\Support;

use ReflectionClass;

class Helper
{
    public static function isEmail(string $var): bool
    {
        return !!filter_var($var, FILTER_VALIDATE_EMAIL);
    }

    /**
     * Get class name from given namespace
     *
     * @throws \ReflectionException
     */
    public static function getClassName(string $namespace, bool $toLowerCase = false): string
    {
        $c = new ReflectionClass($namespace);
        $name = $c->getShortName();

        return $toLowerCase ? strtolower($name) : $name;
    }
}
