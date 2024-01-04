<?php

namespace Boilerplate\Auth\Tests\Feature\Controllers\V1\CheckController;

use Boilerplate\Auth\Enums\ErrorCodes;
use Boilerplate\Auth\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckEmailTest extends TestCase
{
    use RefreshDatabase;

    public function testRegisteredUserCanCheckAccountExistViaEmail(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);

        $response = $this->json('POST', '/api/v1/auth/check-email', [
            'email' => $user->email,
        ]);

        $response->assertOk()
            ->assertJsonStructure([
                'data' => ['email'],
            ])
            ->assertJson([
                'data' => ['email' => $user->email],
            ]);

        $this->assertGuest();
    }

    /** @disabled */
    public function unverifiedEmailShouldNotAbleToLogin(): void
    {
        $user = User::factory()->create(['email_verified_at' => null]);

        $this->json('POST', '/api/v1/auth/check-email', [
            'email' => $user->email,
        ])
            ->assertStatus(401)
            ->assertJson([
                'error_code' => ErrorCodes::UNVERIFIED_EMAIL,
            ]);
    }

    public function testUnregisteredEmailShouldReturnNotFound(): void
    {
        $this->json('POST', '/api/v1/auth/check-email', [
            'email' => 'unregistered@email.com',
        ])->assertStatus(404);
    }

    public function testItShouldValidateEmail(): void
    {
        // email is required
        $this->json('POST', '/api/v1/auth/check-email')
            ->assertStatus(422)
            ->assertJsonStructure([
                'errors' => ['email'],
            ]);

        // invalid email format
        $this->json('POST', '/api/v1/auth/check-email', [
            'email' => 'not_an_email',
        ])->assertStatus(422)
            ->assertJsonStructure([
                'errors' => ['email'],
            ]);

        // should not accept a phone_number
        $this->json('POST', '/api/v1/auth/check-email', [
            'email' => '+639453200575',
        ])->assertStatus(422)
            ->assertJsonStructure([
                'errors' => ['email'],
            ]);
    }
}
