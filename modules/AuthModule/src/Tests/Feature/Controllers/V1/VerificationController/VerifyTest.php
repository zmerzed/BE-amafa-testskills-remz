<?php

namespace Boilerplate\Auth\Tests\Feature\Controllers\V1\VerificationController;

use Boilerplate\Auth\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class VerifyTest extends TestCase
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

    public function testUnverifiedUserCanVerifyEmail(): void
    {
        list($user, $token) = $this->authenticate();

        $this->json(
            'POST',
            '/api/v1/auth/verification/verify',
            ['via' => 'email', 'token' => $user->email_verification_code],
            ['Authorization' => "Bearer $token"]
        )->assertOk();


        tap($user->fresh(), function ($user) {
            $this->assertNotNull($user->email_verified_at);
        });
    }

    public function testUunverifiedUserCanVerifyPhoneNumber(): void
    {
        list($user, $token) = $this->authenticate();

        $this->json(
            'POST',
            '/api/v1/auth/verification/verify',
            ['via' => 'phone_number', 'token' => $user->phone_number_verification_code],
            ['Authorization' => "Bearer $token"]
        )->assertOk();


        tap($user->fresh(), function ($user) {
            $this->assertNotNull($user->phone_number_verified_at);
        });
    }

    public function testTokenShouldBeValidated(): void
    {
        list($user, $token) = $this->authenticate();

        // token is required
        $this->json('POST', '/api/v1/auth/verification/verify', [
            'token' => '',
            'via' => 'email',
        ], ['Authorization' => "Bearer $token"])
            ->assertStatus(422)
            ->assertJsonStructure(['message', 'errors' => ['token']]);

        // token must be equal to user email_verification_token
        $this->json('POST', '/api/v1/auth/verification/verify', [
            'token' => Str::random(5), // some random token
            'via' => 'email',
        ], ['Authorization' => "Bearer $token"])
            ->assertStatus(422)
            ->assertJsonStructure(['message', 'errors' => ['token']]);
    }

    public function testViaShouldBeValidated(): void
    {
        list($user, $token) = $this->authenticate();

        // via is required
        $this->json('POST', '/api/v1/auth/verification/verify', [
            'token' => $user->email_verification_code,
            'via' => '',
        ], ['Authorization' => "Bearer $token"])
            ->assertStatus(422)
            ->assertJsonStructure(['message', 'errors' => ['via']]);

        $this->json('POST', '/api/v1/auth/verification/verify', [
            'token' => Str::random(5),
            'via' => 'facebook' // via not exist
        ], ['Authorization' => "Bearer $token"])
            ->assertStatus(422)
            ->assertJsonStructure(['message', 'errors' => ['via']]);
    }
}
