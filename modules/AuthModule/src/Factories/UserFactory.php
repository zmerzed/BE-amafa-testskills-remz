<?php

namespace Boilerplate\Auth\Factories;

use Boilerplate\Auth\Enums\Role;
use Boilerplate\Auth\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'first_name' => $this->faker->firstName(),
            'last_name'  => $this->faker->lastName(),
            'email' => Str::random() . $this->faker->unique()->safeEmail,
            'phone_number' => '639' . mt_rand(1000000000, 9999999999),
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'remember_token' => Str::random(10),
            'primary_username' => 'email',
        ];
    }

    public function admin(): UserFactory
    {
        return $this->afterCreating(
            function (User $user) {
                $user->syncRoles(Role::ADMIN);
            }
        );
    }
}
