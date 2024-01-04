<?php

namespace Boilerplate\Auth\Tests\Feature\Controllers\V1\AuthController;

use Boilerplate\Auth\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class MeTest extends TestCase
{
    use RefreshDatabase;

    public function testAuthenticatedUserCanGetUserDetails(): void
    {
        $user = Sanctum::actingAs(
            User::factory()->create(['email_verified_at' => null]),
            ['*']
        );
        $token = $user->createToken(config('app.name'))->plainTextToken;

        $this->json('GET', '/api/v1/auth/me', [], ['Authorization' => "Bearer $token"])
            ->assertOk()
            ->assertJsonStructure([
                'data' => ['id', 'first_name', 'last_name', 'email', 'created_at', 'updated_at'],
            ])
            ->assertJson([
                'data' => [
                    'id' => $user->id,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'email' => $user->email,
                ],
            ]);
    }

    public function testUnauthenticatedUserCannotGetUserDetails(): void
    {
        $this->json('GET', '/api/v1/auth/me')->assertStatus(401);
    }
}
