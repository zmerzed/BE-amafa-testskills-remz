<?php

namespace Boilerplate\Auth\Http\Controllers\V1\AccountSettings;

use Illuminate\Http\Response;
use Boilerplate\Auth\Models\User;
use Boilerplate\Auth\Enums\ErrorCodes;
use Illuminate\Support\Facades\Hash;
use Boilerplate\Auth\Http\Controllers\Controller;
use Boilerplate\Auth\Http\Requests\AccountSettings\VerificationTokenRequest;

class VerificationTokenController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(VerificationTokenRequest $request)
    {
        /** @var User $user */
        $user = $request->user();

        if (!$request->has('password') && !$request->has('otp')) {
            return $this->respondWithError(ErrorCodes::AUTHENTICATION_REQUIRED, Response::HTTP_UNAUTHORIZED);
        }

        if ($request->has('password') && !Hash::check($request->input('password'), $user->password)) {
            return $this->respondWithError(ErrorCodes::INVALID_CREDENTIALS, Response::HTTP_UNAUTHORIZED);
        } elseif ($request->has('otp') && !$user->invalidateIfValidOneTimePassword($request->input('otp'))) {
            return $this->respondWithError(ErrorCodes::INVALID_ONE_TIME_PASSWORD, Response::HTTP_UNAUTHORIZED);
        }

        $data = $user->generateVerificationToken();

        return response()->json([
            'data' => [
                'token' => $data['token'],
                'expires_at' => $data['expires_at'],
            ],
        ], Response::HTTP_OK);
    }
}
