<?php

namespace Boilerplate\Auth\Http\Controllers\V1;

use Boilerplate\Auth\Http\Controllers\Controller;
use Boilerplate\Auth\Http\Requests\StoreAvatarRequest;
use Boilerplate\Auth\Models\User;
use Boilerplate\Media\Http\Resources\MediaResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class UserAvatarController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->only(['store', 'destroy']);
    }

    public function show($id): RedirectResponse|MediaResource|JsonResponse
    {
        $user = User::findOrFail($id);
        $wantsMediaResource = request()->has('redirect') && request('redirect') == false;

        if (filled($user->avatar) && $wantsMediaResource) {
            return new MediaResource($user->avatar);
        }

        if (blank($user->avatar) && $wantsMediaResource) {
            return response()->json(null, 404);
        }

        return redirect()->to(optional($user->avatar)->getFullUrl() ?: $user->defaultAvatar());
    }

    public function showThumb($id): RedirectResponse|MediaResource|JsonResponse
    {
        $user = User::findOrFail($id);

        $wantsMediaResource = request()->has('redirect') && request('redirect') == false;

        if (filled($user->avatar) && $wantsMediaResource) {
            return new MediaResource($user->avatar);
        }

        if (blank($user->avatar) && $wantsMediaResource) {
            return response()->json(null, 404);
        }

        return redirect()->to(optional($user->avatar)->getFullUrl('thumb') ?: $user->defaultAvatar());
    }

    /**
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist
     * @throws \Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig
     */
    public function store(User $user, StoreAvatarRequest $request): MediaResource
    {
        $this->authorize('update', [$user]);

        $data = $request->validated();

        // Hashing file name
        $name = md5(uniqid('AVATAR' . $user->id, true));
        $fileName = $name . '.' . $data['avatar']->extension();

        $avatar = $user->addMedia($data['avatar'])
            ->usingName($name)
            ->usingFileName($fileName)
            ->toMediaCollection('avatar');

        return new MediaResource($avatar);
    }

    /**
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy(User $user): JsonResponse
    {
        $this->authorize('update', [$user]);

        $user->avatar->delete();

        return $this->respondWithEmptyData();
    }
}
