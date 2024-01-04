<?php

namespace Boilerplate\Auth\Http\Controllers\V1;

use Boilerplate\Auth\Http\Controllers\Controller;
use Boilerplate\Auth\Http\Requests\ForgotPasswordRequest;
use Boilerplate\Auth\Models\PasswordReset;
use Boilerplate\Auth\Models\User;
use Boilerplate\Auth\Notifications\PasswordReset as PasswordResetNotification;
use Boilerplate\Auth\Support\Helper;
use Boilerplate\Sms\Notifications\Channels\SmsChannel;
use Illuminate\Http\JsonResponse;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Create a new password reset instance for resetting password.
     * And send password reset email to user
     */
    public function __invoke(ForgotPasswordRequest $request): JsonResponse
    {
        $user = User::hasUsername($request->username)->first();
        // Delete old password reset
        // $user->passwordReset()->delete();
        PasswordReset::where('user_id', $user->id)->delete();
        // Create new one
        $passwordReset = $user->passwordReset()->create();

        $user->notify(new PasswordResetNotification($this->getVia($request->username)));

        return response()->json([
            'data' => [
                'username' => $request->username,
                'expires_at' => $passwordReset->expires_at,
                'created_at' => $passwordReset->created_at,
            ],
        ], 201);
    }

    /**
     * Auto-detect where to send the reset token
     */
    private function getVia(string $username): array
    {
        if (Helper::isEmail($username)) {
            return ['mail'];
        }

        return [SmsChannel::class];
    }
}
