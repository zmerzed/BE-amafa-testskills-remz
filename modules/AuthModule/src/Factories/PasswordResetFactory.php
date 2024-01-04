<?php

namespace Boilerplate\Auth\Factories;

use Boilerplate\Auth\Models\PasswordReset;
use Boilerplate\Auth\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PasswordResetFactory extends Factory
{
    protected $model = PasswordReset::class;

    public function definition(): array
    {
        return [
            //
        ];
    }

    public function configure(): PasswordResetFactory
    {
        return $this->afterMaking(
            function (PasswordReset $passwordReset) {
                if (!$passwordReset->user_id) {
                    $passwordReset->user_id = User::factory()
                        ->create()
                        ->getKey();
                }
            }
        );
    }
}
