<?php

namespace Boilerplate\Auth\Policies;

use Boilerplate\Auth\Enums\Permission;
use Boilerplate\Auth\Enums\Role;
use Boilerplate\Auth\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    public function before(?User $user, $ability): ?bool
    {
        if ($user->hasRole(Role::ADMIN)) {
            return true;
        }

        return null;
    }

    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo(Permission::VIEW_USERS);
    }

    public function update(User $user, User $model): bool
    {
        return $user->getKey() === $model->getKey();
    }

    public function view(User $user, User $model): bool
    {
        return true;
    }

    public function delete(User $user, User $model): bool
    {
        return false;
    }
}
