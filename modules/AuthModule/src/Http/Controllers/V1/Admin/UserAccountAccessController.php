<?php

namespace Boilerplate\Auth\Http\Controllers\V1\Admin;

use Boilerplate\Auth\Http\Controllers\Controller;
use Boilerplate\Auth\Http\Resources\UserResource;
use Boilerplate\Auth\Models\User;

class UserAccountAccessController extends Controller
{
    /**
     * Disable user
     * Block app access
     */
    public function blockUserAccess(User $user): UserResource
    {
        if (blank($user->blocked_at)) {
            $user->blocked_at = now();
            $user->save();

            $user->tokens()->delete();
        }

        return UserResource::make($user->load('avatar'));
    }

    /**
     * Enable user
     * Grant app access
    */
    public function grantUserAccess(User $user): UserResource
    {
        if (filled($user->blocked_at)) {
            $user->blocked_at = null;
            $user->save();
        }

        return UserResource::make($user->load('avatar'));
    }
}
