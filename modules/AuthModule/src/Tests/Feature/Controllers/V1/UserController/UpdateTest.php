<?php

namespace Boilerplate\Auth\Tests\Feature\Controllers\V1\UserController;

use Boilerplate\Auth\Models\User;
use Boilerplate\Auth\Support\ValidatesPhone;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UpdateTest extends TestCase
{
    use WithFaker;
    use ValidatesPhone;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('app:acl:sync');
    }

    public function testAdminCanUpdateUser(): void
    {
        $this->artisan('app:acl:sync');

        $user = User::factory()
            ->admin()
            ->create();

        Sanctum::actingAs($user);

        $otherUser = User::factory()
            ->create();

        $payload = [
            'first_name' => $this->faker->name,
            'last_name' => $this->faker->name,
            'phone_number' => $this->faker->e164PhoneNumber(),
            'email' => $this->faker->safeEmail,
        ];
        $this->putJson("/api/v1/users/{$otherUser->getKey()}", $payload)
            ->assertOk();

        $this->assertDatabaseHas(
            'users',
            array_merge(
                $payload,
                [
                    'phone_number' => $this->cleanPhoneNumber($payload['phone_number']),
                    'email_verified_at' => null,
                    'phone_number_verified_at' => null,
                ]
            )
        );
    }

    public function testUserCannotUpdateOtherUser(): void
    {
        $this->artisan('app:acl:sync');

        $user = User::factory()
            ->create();
        Sanctum::actingAs($user);

        $otherUser = User::factory()
            ->create();

        $this->deleteJson("/api/v1/users/{$otherUser->getKey()}")
            ->assertForbidden();
    }
}
