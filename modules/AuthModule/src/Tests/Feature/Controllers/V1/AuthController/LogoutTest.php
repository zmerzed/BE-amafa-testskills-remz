<?php

namespace Boilerplate\Auth\Tests\Feature\Controllers\V1\AuthController;

use Boilerplate\Auth\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

    public function testAuthenticatedUserCanLogout(): void
    {
        $user = Sanctum::actingAs(
            User::factory()->create(['email_verified_at' => null]),
            ['*']
        );
        $token = $user->createToken(config('app.name'))->plainTextToken;

        $this->json('POST', '/api/v1/auth/logout', [], ['Authorization' => "Bearer $token"])->assertOk();

        $this->assertGuest('web');
    }

    public function testUnauthenticatedUserCannotLogout(): void
    {
        $this->json('POST', '/api/v1/auth/logout')->assertStatus(401);
    }
}
