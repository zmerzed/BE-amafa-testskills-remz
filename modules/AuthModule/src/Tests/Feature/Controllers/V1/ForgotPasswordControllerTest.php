<?php

namespace Boilerplate\Auth\Tests\Feature\Controllers\V1;

use Boilerplate\Auth\Models\PasswordReset as PasswordResetModel;
use Boilerplate\Auth\Models\User;
use Boilerplate\Auth\Notifications\PasswordReset as PasswordResetNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ForgotPasswordControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testUserCanRequestResetPasswordTokenViaEmail(): void
    {
        $user = User::factory()->create();

        Notification::fake();
        Notification::assertNothingSent();

        $this->json('POST', '/api/v1/auth/forgot-password', [
            'username' => $user->email,
        ])->assertStatus(201)
            ->assertJsonStructure([
                'data' => ['username', 'expires_at', 'created_at'],
            ])
            ->assertJson([
                'data' => ['username' => $user->email],
            ]);

        $this->assertDatabaseHas('password_resets', [
            'user_id' => $user->id,
        ]);

        PasswordResetModel::find($user->id);

        Notification::assertSentTo($user, PasswordResetNotification::class);
    }

    public function testUserCanRequestResetPasswordTokenViaSms(): void
    {
        $user = User::factory()->create();

        Notification::fake();
        Notification::assertNothingSent();

        $this->json('POST', '/api/v1/auth/forgot-password', [
            'username' => $user->phone_number,
        ])->assertStatus(201)
            ->assertJsonStructure([
                'data' => ['username', 'expires_at', 'created_at'],
            ])
            ->assertJson([
                'data' => ['username' => $user->phone_number],
            ]);

        $this->assertDatabaseHas('password_resets', [
            'user_id' => $user->id,
        ]);

        PasswordResetModel::find($user->id);

        Notification::assertSentTo($user, PasswordResetNotification::class);
    }

    public function testUsernameMustBeValidated(): void
    {
        // Email field is required
        $this->json('POST', '/api/v1/auth/forgot-password', [
            'username' => '',
        ])
            ->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors' => ['username'],
            ]);

        // Email must be valid
        $this->json('POST', '/api/v1/auth/forgot-password', [
            'username' => 'not_a_valid_username',
        ])
            ->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors' => ['username'],
            ]);

        // Email must exists
        $this->json('POST', '/api/v1/auth/forgot-password', [
            'username' => 'notexisting@email.xxx',
        ])
            ->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors' => ['username'],
            ]);
    }
}
