<?php

namespace Boilerplate\Auth\Enums;

use BenSampo\Enum\Enum;

final class Role extends Enum
{
    const ADMIN = 'ADMIN';

    const USER = 'USER';

    /**
     * The default role for all new added user
     */
    public static function default(): string
    {
        return Role::USER;
    }

    public static function defaultPermissions(): array
    {
        return [
            // Adding "ALL" will give all permission
            Role::ADMIN => ['ALL'],

            Role::USER => [],
        ];
    }

    public static function getPermissions(string $role): array
    {
        return Role::defaultPermissions()[$role] ?? [];
    }
}
