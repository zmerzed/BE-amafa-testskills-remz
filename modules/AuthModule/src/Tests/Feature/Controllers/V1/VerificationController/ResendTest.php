<?php

namespace Boilerplate\Auth\Tests\Feature\Controllers\V1\VerificationController;

use Boilerplate\Auth\Models\User;
use Boilerplate\Auth\Notifications\VerifyEmail;
use Boilerplate\Auth\Notifications\VerifyPhoneNumber;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ResendTest extends TestCase
{
    use RefreshDatabase;

    private function authenticate(): array
    {
        $user = Sanctum::actingAs(
            User::factory()->create(['email_verified_at' => null]),
            ['*']
        );
        $token = $user->createToken(config('app.name'))->plainTextToken;

        return [$user, $token];
    }

    public function testUnverifiedUserCanResendEmailVerificationToken(): void
    {
        Notification::fake();
        Notification::assertNothingSent();

        list($user, $token) = $this->authenticate();

        $this->json(
            'POST',
            '/api/v1/auth/verification/resend',
            ['via' => 'email'],
            ['Authorization' => "Bearer $token"]
        )->assertOk();

        Notification::assertSentTo(
            $user,
            VerifyEmail::class,
            function ($notification) use ($user) {
                $notifier = $notification->toMail($user);
                return $notifier->user === $user;
            }
        );
    }

    public function testUnverifiedUserCanResendPhoneNumberVerificationToken(): void
    {
        Notification::fake();
        Notification::assertNothingSent();

        list($user, $token) = $this->authenticate();

        $this->json(
            'POST',
            '/api/v1/auth/verification/resend',
            ['via' => 'phone_number'],
            ['Authorization' => "Bearer $token"]
        )->assertOk();

        Notification::assertSentTo($user, VerifyPhoneNumber::class);
    }
}
