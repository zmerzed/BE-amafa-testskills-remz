<?php

namespace Boilerplate\Auth\Http\Controllers\V1;

use Boilerplate\Auth\Actions\SendVerificationCode;
use Boilerplate\Auth\Enums\UsernameType;
use Boilerplate\Auth\Http\Controllers\Controller;
use Boilerplate\Auth\Http\Requests\ResendVerificationRequest;
use Boilerplate\Auth\Http\Requests\VerificationRequest;
use Boilerplate\Auth\Http\Resources\UserResource;
use Illuminate\Support\Carbon;

class VerificationController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Email Verification Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling email verification for any
    | user that recently registered with the application. Emails may also
    | be re-sent if the user didn't receive the original email message.
    |
    */

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth']);
        $this->middleware('throttle:6,1');
    }

    /**
     * Verify user email or phone number
     *
     * @param VerificationRequest $request
     * @return UserResource
     */
    public function verify(VerificationRequest $request)
    {
        $user = $request->user();

        if ($request->via == UsernameType::EMAIL) {
            $user->email_verified_at = Carbon::now();
        } else {
            $user->phone_number_verified_at = Carbon::now();
        }

        $user->save();

        return UserResource::make($user->load('avatar'));
    }

    /**
     * Resent Verification via email
     *
     * @return UserResource
     */
    public function resend(ResendVerificationRequest $request, SendVerificationCode $sendVerificationCode)
    {
        $user = $request->user();

        $sendVerificationCode->execute($user, $request->via);

        return UserResource::make($user->load('avatar'));
    }
}
