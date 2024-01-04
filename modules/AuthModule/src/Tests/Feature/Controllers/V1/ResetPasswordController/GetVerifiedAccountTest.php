<?php

namespace Boilerplate\Auth\Tests\Feature\Controllers\V1\ResetPasswordController;

use Boilerplate\Auth\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetVerifiedAccountTest extends TestCase
{
    use RefreshDatabase;

    public function testShouldBeAbleToSearchForEmailOrPhoneNumber(): void
    {
        $user = User::factory()->create();

        $this->json(
            'POST',
            '/api/v1/auth/reset-password/get-verified-account',
            ['email' => $user->email]
        )
            ->assertOk()
            ->assertJsonStructure([
                'is_email_verified',
                'is_phone_verified',
                'verified_account',
            ]);
    }
}
