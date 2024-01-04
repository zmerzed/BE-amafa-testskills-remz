<?php

namespace Boilerplate\Auth\Http\Controllers\V1;

use Boilerplate\Auth\Http\Controllers\Controller;
use Boilerplate\Auth\Http\Requests\CheckResetPasswordTokenRequest;
use Boilerplate\Auth\Http\Requests\ResetPasswordRequest;
use Boilerplate\Auth\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Update user password and remove all password_reset associated to user
     */
    public function __invoke(ResetPasswordRequest $request): JsonResponse
    {
        DB::transaction(function () use ($request) {
            // Change user password
            $user = User::hasUsername($request->username)->first();
            $user->password = Hash::make($request->password);
            $user->save();

            $user->passwordReset()->delete();
        });

        return response()->json([
            'message' => trans('passwords.reset'),
        ]);
    }

    public function checkToken(CheckResetPasswordTokenRequest $request): JsonResponse
    {
        $user = User::hasUsername($request->username)->first();
        $passwordReset = $user->passwordReset;
        $passwordReset->makeVisible(['token']);

        return response()->json([
            'data' => [
                'username' => $request->username,
                'token' => $passwordReset->token,
                'expires_at' => $passwordReset->expires_at,
                'created_at' => $passwordReset->created_at,
            ],
        ]);
    }

    public function getVerifiedAccount(Request $request): JsonResponse
    {
        $user = User::where('email', $request->get('email'))->first();

        return response()->json([
            'is_email_verified' => $user->isEmailVerified(),
            'is_phone_verified' => $user->isPhoneNumberVerified(),
            'verified_account' => $user->verifiedAccount,
        ]);
    }
}
