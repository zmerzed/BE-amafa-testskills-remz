<?php

namespace Boilerplate\Auth\Tests\Feature\Controllers\V1;

use Boilerplate\Auth\Models\PasswordReset;
use Boilerplate\Auth\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ResetPasswordControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testUserCanResetPassword(): void
    {
        $user = User::factory()->create();
        $pr = $user->passwordReset()->create();

        $response = $this->json('POST', '/api/v1/auth/reset-password', [
            'username' => $user->email,
            'token' => $pr->token,
            'password' => $pw = 'new-password',
            'password_confirmation' => $pw,
        ]);

        $response->assertOk();

        $now = now();

        // password_reset token must be remove from password_resets table
        $this->assertDatabaseMissing('password_resets', [
            'user_id' => $user->id,
            'token' => $pr->token,
            'expires_at' => $now,
            'created_at' => $now,
        ]);

        // test login with new password
        $res = $this->json('POST', '/api/v1/auth/login', [
            'username' => $user->email,
            'password' => $pw,
        ])->assertOk();
    }

    public function testUsernameMustBeValidatedOnPasswordReset(): void
    {
        $user = User::factory()->create();
        $pr = PasswordReset::factory()->create(['user_id' => $user->id]);

        // not a valid email
        $this->json('POST', '/api/v1/auth/reset-password', [
            'username' => 'not_an_email',
            'token' => $pr->token,
            'password' => $pw = 'new-password',
            'password_confirmation' => $pw,
        ])
            ->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors' => ['username'],
            ]);

        // email does not exist on user table
        $this->json('POST', '/api/v1/auth/reset-password', [
            'username' => 'some.random@email.xxx',
            'token' => $pr->token,
            'password' => $pw = 'new-password',
            'password_confirmation' => $pw,
        ])
            ->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors' => ['username'],
            ]);
    }

    public function testTokenMustBeValidatedOnPasswordReset(): void
    {
        $user = User::factory()->create();
        $pr = PasswordReset::factory()->create(['user_id' => $user->id]);

        // token is required
        $r = $this->json('POST', '/api/v1/auth/reset-password', [
            'username' => $user->email,
            'token' => '',
            'password' => $pw = 'new-password',
            'password_confirmation' => $pw,
        ])
            ->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors' => ['token'],
            ]);

        // invalid token
        $this->json('POST', '/api/v1/auth/reset-password', [
            'username' => $user->email,
            'token' => 'some_random_token',
            'password' => $pw = 'new-password',
            'password_confirmation' => $pw,
        ])
            ->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors' => ['token'],
            ]);

        // token and email not match
        $pr = PasswordReset::factory()->create();
        $this->json('POST', '/api/v1/auth/reset-password', [
            'username' => $user->email,
            'token' => $pr->token,
            'password' => $pw = 'new-password',
            'password_confirmation' => $pw,
        ])
            ->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors' => ['token'],
            ]);
    }

    public function testPasswordMustBeValidatedOnPasswordReset(): void
    {
        $user = User::factory()->create();
        $pr = PasswordReset::factory()->create(['user_id' => $user->id]);
        $pw = 'new-password';

        // password is required
        $r = $this->json('POST', '/api/v1/auth/reset-password', [
            'username' => $user->email,
            'token' => $pr->token,
            'password' => '',
            'password_confirmation' => $pw,
        ])
            ->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors' => ['password'],
            ]);

        // password must be 8 char above
        $this->json('POST', '/api/v1/auth/reset-password', [
            'username' => $user->email,
            'token' => $pr->token,
            'password' => '123',
            'password_confirmation' => $pw,
        ])
            ->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors' => ['password'],
            ]);

        // password and password confirmation must be equal
        $pr = PasswordReset::factory()->create();
        $this->json('POST', '/api/v1/auth/reset-password', [
            'username' => $user->email,
            'token' => $pr->token,
            'password' => $pw,
            'password_confirmation' => 'some-random-password',
        ])
            ->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors' => ['password'],
            ]);
    }
}
