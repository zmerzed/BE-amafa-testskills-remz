<?php

namespace Boilerplate\Auth\Http\Controllers\V1;

use Boilerplate\Auth\Mail\AccountDeletion;
use Boilerplate\Auth\Models\User;
use Illuminate\Support\Facades\Mail;
use Boilerplate\Auth\Http\Controllers\Controller;
use Boilerplate\Auth\Http\Requests\AccountDeletionRequest;
use Boilerplate\Auth\Http\Requests\AccountDeletionConfirmRequest;
use Boilerplate\Auth\Support\Helper;
use Boilerplate\Auth\Support\OneTimePassword\InteractsWithOneTimePassword;

class AccountDeletionController extends Controller
{
    use InteractsWithOneTimePassword;

    public function store(AccountDeletionRequest $request)
    {
        if (Helper::isEmail($request->username)) {
            $user = User::hasUsername($request->username)->firstOrFail();
            Mail::to($user->email)->send(new AccountDeletion($user->email));

            return response()->json([
                'message' => 'Account deletion email sent.',
            ]);
        }

        $this->sendOneTimePassword($request->username);

        return response()->json([
            'message' => 'Account deletion OTP sent.',
            'phone_number' => $request->username,
            'otp_required' => true,
        ]);
    }

    public function confirm(AccountDeletionConfirmRequest $request)
    {
        $user = User::where('phone_number', $request->phone_number)->firstOrFail();
        $validOtp = $this->hasValidOneTimePassword($user->phone_number, $request->input('otp'));

        if (!$validOtp) {
            return response()->json([
                'message' => 'Invalid OTP.',
                'errors' => ['otp' => ['Invalid OTP.']]
            ], 422);
        }

        $user->delete();

        return response()->json([
            'message' => 'Account deleted.',
        ]);
    }
}
