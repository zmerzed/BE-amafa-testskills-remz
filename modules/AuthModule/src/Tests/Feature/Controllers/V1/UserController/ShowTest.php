<?php

namespace Boilerplate\Auth\Tests\Feature\Controllers\V1\UserController;

use Boilerplate\Auth\Models\User;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ShowTest extends TestCase
{

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('app:acl:sync');
    }

    public function testGetUserDetails(): void
    {
        $user = User::factory()
            ->create();

        Sanctum::actingAs($user);

        $otherUser = User::factory()
            ->create();

        $this->getJson("/api/v1/users/{$otherUser->getKey()}")
            ->assertOk()
            ->assertJsonStructure(
                [
                    'data' => ['id', 'first_name', 'last_name', 'email', 'created_at', 'updated_at'],
                ]
            )
            ->assertJson(
                [
                    'data' => $otherUser->only(['id', 'first_name', 'last_name', 'email']),
                ]
            );
    }
}
