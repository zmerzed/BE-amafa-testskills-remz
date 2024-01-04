<?php

namespace Boilerplate\Auth\Observers;

use Boilerplate\Auth\Enums\Role;
use Boilerplate\Auth\Models\User;
use Spatie\Permission\Models\Role as SpatieRole;

class UserObserver
{
    public function creating(User $user): void
    {
        $user->email_verification_code = $this->randomCode();
        $user->phone_number_verification_code = $this->randomCode();
    }

    public function created(User $user): void
    {
        if (SpatieRole::whereName(Role::default())->count() > 0) {
            if ($user->roles()->count() == 0) {
                $user->assignRole(Role::default());
            }
        }
    }

    public function updating(User $user): void
    {
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
            $user->email_verification_code = $this->randomCode();
        }

        if ($user->isDirty('phone_number')) {
            $user->phone_number_verified_at = null;
            $user->phone_number_verification_code = $this->randomCode();
        }
    }

    public function updated(User $user): void
    {
        //
    }

    public function deleted(User $user): void
    {
        //
    }

    public function restored(User $user): void
    {
        //
    }

    public function forceDeleted(User $user): void
    {
        //
    }

    private function randomCode(): string
    {
        return (string)random_int(10000, 99999);
    }
}
