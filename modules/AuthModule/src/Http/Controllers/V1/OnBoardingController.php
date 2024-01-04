<?php

namespace Boilerplate\Auth\Http\Controllers\V1;

use Boilerplate\Auth\Http\Controllers\Controller;
use Boilerplate\Auth\Http\Resources\UserResource;
use Boilerplate\Auth\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class OnBoardingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Set the email of the user.
     */
    public function email(Request $request): UserResource
    {
        Gate::authorize('update-onboarding-details');

        /** @var User $user */
        $user = $request->user();

        $payload = $request->validate([
            'email' => [
                'required',
                "unique:users,email,{$user->id},id",
            ],
        ]);

        $user->email = $payload['email'];
        $user->save();

        return UserResource::make($user->fresh('avatar'));
    }

    /**
     * Handles the request in completing the onboarding process.
     */
    public function complete(Request $request): UserResource
    {
        /** @var User $user */
        $user = $request->user();

        $user->onboard();

        return UserResource::make($user->fresh('avatar'));
    }
}
