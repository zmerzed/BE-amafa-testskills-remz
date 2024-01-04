<?php

namespace Boilerplate\Auth\Http\Controllers\V1;

use Boilerplate\Auth\Enums\ErrorCodes;
use Boilerplate\Auth\Http\Controllers\Controller;
use Boilerplate\Auth\Http\Requests\LoginRequest;
use Boilerplate\Auth\Http\Resources\UserResource;
use Boilerplate\Auth\Models\User;
use Boilerplate\Auth\Support\ValidatesEmail;
use Boilerplate\Auth\Support\ValidatesPhone;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\NewAccessToken;

class AuthController extends Controller
{
    use ValidatesPhone;
    use ValidatesEmail;

    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    public function __construct()
    {
        $this->middleware('auth', ['except' => ['login']]);
        $this->middleware('throttle:60,1', ['except' => ['me']]);
    }

    /**
     * Authenticate user using username and password
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(LoginRequest $request)
    {
        /** @var User */
        $user = User::query()
            ->with('avatar')
            ->withBlocked()
            ->hasUsername($request->input('username'))
            ->first();

        if (!$user) {
            return $this->respondWithError(ErrorCodes::INVALID_CREDENTIALS, Response::HTTP_UNAUTHORIZED);
        }

        if ($user->isBlocked()) {
            return $this->respondWithError(ErrorCodes::ACCOUNT_BLOCKED, Response::HTTP_UNAUTHORIZED);
        }

        if ($user->isEmailPrimary() && $this->isPhone($request->input('username'))) {
            return $this->respondWithError(ErrorCodes::AUTHENTICATION_EMAIL_REQUIRED, Response::HTTP_UNAUTHORIZED);
        }

        if ($user->isPhonePrimary() && $this->isEmail($request->input('username'))) {
            return $this->respondWithError(
                ErrorCodes::AUTHENTICATION_PHONE_NUMBER_REQUIRED,
                Response::HTTP_UNAUTHORIZED
            );
        }

        if (!$request->has('password') && !$request->has('otp')) {
            return $this->respondWithError(ErrorCodes::AUTHENTICATION_REQUIRED, Response::HTTP_UNAUTHORIZED);
        }

        if ($request->has('password') && !Hash::check($request->input('password'), $user->password)) {
            return $this->respondWithError(ErrorCodes::INVALID_CREDENTIALS, Response::HTTP_UNAUTHORIZED);
        }

        if ($request->has('otp')) {
            // Invalidate the one time password once it is valid, else return an invalid otp error.
            if (!$user->invalidateIfValidOneTimePassword($request->input('otp'))) {
                return $this->respondWithError(ErrorCodes::INVALID_ONE_TIME_PASSWORD, Response::HTTP_UNAUTHORIZED);
            }
        }

        // check if the user use email to login
        // disable for now
        // if ($user->email == $data['username']) {
        //     // check if email is verified
        //     if (!$user->isEmailVerified()) {
        //         return $this->respondWithError(ErrorCodes::UNVERIFIED_EMAIL, 401);
        //     }
        // } else {
        //     // check if phone number is verified
        //     if (!$user->isPhoneNumberVerified()) {
        //         return $this->respondWithError(ErrorCodes::UNVERIFIED_PHONE_NUMBER, 401);
        //     }
        // }

        /** @var NewAccessToken $newAccessToken */
        $newAccessToken = $user->createToken($request->header('user-agent'));

        return $this->respondWithToken($newAccessToken->plainTextToken, UserResource::make($user));
    }

    public function me(): UserResource
    {
        return new UserResource(auth()->user()->load('avatar'));
    }

    /**
     * Log the user out (Invalidate the token).
     */
    public function logout(): JsonResponse
    {
        auth()->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Successfully logged out']);
    }
}
