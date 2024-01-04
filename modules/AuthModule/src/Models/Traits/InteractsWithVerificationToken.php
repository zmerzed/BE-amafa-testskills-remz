<?php

namespace Boilerplate\Auth\Models\Traits;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

trait InteractsWithVerificationToken
{
    /*
    |--------------------------------------------------------------------------
    | Verification Token
    |--------------------------------------------------------------------------
    | A very simple implementation for verifying if an entity tells who they are.
    | Best used in verifying using users password in exchange for a
    | verification token in order to proceed to a request.
    */

    public function generateVerificationToken(Carbon $expiresAt = null): array
    {
        if (blank($expiresAt)) {
            $expiresAt = now()->addMinutes(60);
        }

        $token = hash('sha256', $plainTextToken = Str::random(80));

        DB::table('verification_tokens')->insert([
            'verifiable_type' => $this->getMorphClass(),
            'verifiable_id' => $this->getKey(),
            'token' => $token,
            'expires_at' => $expiresAt,
        ]);

        return [
            'token' => $plainTextToken,
            'expires_at' => $expiresAt,
        ];
    }

    public function hasValidVerificationToken(string $token): bool
    {
        return DB::table('verification_tokens')
            ->where('token', hash('sha256', $token))
            ->where('expires_at', '>', now())
            ->exists();
    }
}
