<?php

namespace Boilerplate\Auth\Http\Controllers\V1;

use Boilerplate\Auth\Http\Controllers\Controller;
use Boilerplate\Auth\Http\Requests\StoreAvatarRequest;
use Boilerplate\Auth\Models\User;
use Boilerplate\Media\Http\Resources\MediaResource;

class ProfileAvatarController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Store new user avatar
     */
    public function store(StoreAvatarRequest $request): MediaResource
    {
        $data = $request->validated();

        /** @var User $user */
        $user = auth()->user();

        $avatar = $user->setAvatar($data['avatar']);

        return new MediaResource($avatar);
    }
}
