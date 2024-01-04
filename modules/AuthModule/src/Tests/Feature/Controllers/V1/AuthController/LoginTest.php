<?php

namespace Boilerplate\Auth\Tests\Feature\Controllers\V1\AuthController;

use Boilerplate\Auth\Enums\ErrorCodes;
use Boilerplate\Auth\Enums\UsernameType;
use Boilerplate\Auth\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function testRegisteredUserCanLoginViaEmail(): void
    {
        $user = Sanctum::actingAs(
            User::factory()->create(['email_verified_at' => now()]),
            ['*']
        );

        $response = $this->json('POST', '/api/v1/auth/login', [
            'username' => $user->email,
            'password' => 'password',
        ]);

        $response->assertOk()
            ->assertJsonStructure([
                'data' => ['access_token', 'token_type', 'expires_in'],
            ]);

        $this->assertAuthenticated();
        $this->assertAuthenticatedAs($user);
    }

    public function testRegisteredUserCanLoginViaPhoneNumber(): void
    {
        $user = Sanctum::actingAs(
            User::factory()->create([
                'primary_username' => UsernameType::PHONE_NUMBER,
                'phone_number_verified_at' => now(),
            ]),
            ['*']
        );

        $response = $this->json('POST', '/api/v1/auth/login', [
            'username' => $user->phone_number,
            'password' => 'password',
            'otp' => '00000',
        ])
            ->assertOk()
            ->assertJsonStructure([
                'data' => ['access_token', 'token_type', 'expires_in'],
            ]);

        $this->assertAuthenticated();
        $this->assertAuthenticatedAs($user);
    }

    /** @disabled */
    public function unverifiedUserEmailCannotLogIn(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        $this->json('POST', '/api/v1/auth/login', [
            'username' => $user->email,
            'password' => 'password',
        ])->assertStatus(401)
            ->assertJson([
                'error_code' => ErrorCodes::UNVERIFIED_EMAIL,
            ]);
    }

    /** @disabled */
    public function unverifiedUserPhoneNumberCannotLogIn(): void
    {
        $user = User::factory()->create([
            'phone_number_verified_at' => null,
        ]);

        $this->json('POST', '/api/v1/auth/login', [
            'username' => $user->phone_number,
            'password' => 'password',
        ])->assertStatus(401)
            ->assertJson([
                'error_code' => ErrorCodes::UNVERIFIED_PHONE_NUMBER,
            ]);
    }

    public function testUnregisteredUsersCannotLogIn(): void
    {
        $this->json('POST', '/api/v1/auth/login', [
            'username' => 'some.random@email.com',
            'password' => 'password',
        ])->assertStatus(401);
    }

    /** @disabled */
    public function wrongUserPasswordCannotLogIn(): void
    {
        $user = Sanctum::actingAs(
            User::factory()->create(['email_verified_at' => null]),
            ['*']
        );

        $this->json('POST', '/api/v1/auth/login', [
            'username' => $user->email,
            'password' => 'th!$_!$_n()t_ah_p@ssw0rd',
        ])->assertStatus(401);
    }
}
