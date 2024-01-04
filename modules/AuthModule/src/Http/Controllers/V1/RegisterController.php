<?php

namespace Boilerplate\Auth\Http\Controllers\V1;

use Boilerplate\Auth\Actions\SendVerificationCode;
use Boilerplate\Auth\Enums\UsernameType;
use Boilerplate\Auth\Http\Controllers\Controller;
use Boilerplate\Auth\Http\Requests\RegisterUserRequest;
use Boilerplate\Auth\Http\Resources\UserResource;
use Boilerplate\Auth\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    public function __construct()
    {
        $this->middleware('guest');
    }

    public function __invoke(RegisterUserRequest $request, SendVerificationCode $sendVerificationCode): JsonResponse
    {
        $user = new User();
        $user->fill($request->safe()->only(['first_name', 'last_name', 'email']));

        $usesEmailAuthentication = $request->has('email') && $request->has('password');

        if ($usesEmailAuthentication) {
            $user->email = $request->input('email');
            $user->password = Hash::make($request->input('password'));
            $user->primary_username = UsernameType::EMAIL;
        } else {
            $user->phone_number = $request->input('phone_number');
            $user->primary_username = UsernameType::PHONE_NUMBER;
        }

        $user->save();

        // $sendVerificationCode->execute($user);

        $newAccessToken = $user->createToken($request->header('user-agent', config('app.name')));

        return $this->respondWithToken($newAccessToken->plainTextToken, new UserResource($user));
    }
}
