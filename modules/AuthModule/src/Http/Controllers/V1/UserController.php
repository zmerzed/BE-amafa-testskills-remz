<?php

namespace Boilerplate\Auth\Http\Controllers\V1;

use Boilerplate\Auth\Http\Controllers\Controller;
use Boilerplate\Auth\Http\Requests\User\UpdateRequest;
use Boilerplate\Auth\Http\Resources\UserResource;
use Boilerplate\Auth\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
        $this->authorizeResource(User::class);
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        $users = QueryBuilder::for(User::class)
            ->allowedIncludes('avatar')
            ->allowedSorts('first_name', 'last_name', 'email', 'phone_number', 'blocked_at', 'created_at')
            ->allowedFilters([
                AllowedFilter::scope('search'),
                AllowedFilter::scope('with_blocked')->ignore(null),
                AllowedFilter::scope('only_blocked')->ignore(null),
            ])
            ->defaultSort('first_name')
            ->paginate($request->perPage());

        return UserResource::collection($users);
    }

    public function show(User $user): UserResource
    {
        $user->load('avatar');

        return new UserResource($user);
    }

    public function update(UpdateRequest $request, User $user): UserResource
    {
        $user->update($request->validated());

        $user->load('avatar');

        return new UserResource($user);
    }

    public function destroy(User $user): JsonResponse
    {
        $user->delete();

        return response()->json([], Response::HTTP_OK);
    }
}
