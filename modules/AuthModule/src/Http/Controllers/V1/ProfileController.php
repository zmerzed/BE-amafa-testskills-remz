<?php

namespace Boilerplate\Auth\Http\Controllers\V1;

use Boilerplate\Auth\Http\Controllers\Controller;
use Boilerplate\Auth\Http\Requests\UpdateProfileRequest;
use Boilerplate\Auth\Http\Resources\UserResource;
use Boilerplate\Auth\Models\User;
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show current authenticated user profile
     */
    public function index(): UserResource
    {
        return new UserResource(auth()->user()->load('avatar'));
    }

    /**
     * Update current authenticated user profile
     */
    public function update(UpdateProfileRequest $request): UserResource
    {
        $user = DB::transaction(function () use ($request) {
            /** @var User $user */
            $user = auth()->user();

            $user->update($request->validated());

            if ($request->has('avatar')) {
                /**
                 * If the avatar parameter value is null,
                 * we will assume that the user was trying to remove the avatar.
                 *
                 * Else it was trying to set a new avatar.
                 */
                $avatarId = $request->input('avatar');
                if (is_null($avatarId)) {
                    $user->removeAvatar();
                } else {
                    $user->setAvatarByMediaId($avatarId);
                }
            }

            return $user;
        });

        return new UserResource($user->load('avatar'));
    }
}
