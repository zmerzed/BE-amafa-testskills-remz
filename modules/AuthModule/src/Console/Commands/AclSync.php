<?php

namespace Boilerplate\Auth\Console\Commands;

use Boilerplate\Auth\Enums\Permission;
use Boilerplate\Auth\Enums\Role;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission as PermissionModel;
use Spatie\Permission\Models\Role as RoleModel;

class AclSync extends Command
{
    protected $signature = 'app:acl:sync';

    protected $description = 'Sync roles and permissions to database';

    public function handle(): void
    {
        DB::transaction(function () {
            $this->syncRoles();
            $this->syncPermissions();
            $this->syncRolesDefaultPermissions();
        });
    }

    private function syncRoles(): void
    {
        $this->line("Syncing roles!");

        $roles = Role::getValues();

        foreach ($roles as $role) {
            RoleModel::firstOrCreate(['name' => $role]);
        }

        $this->info("Roles successfully synced!");
    }

    private function syncPermissions(): void
    {
        $this->line("Syncing permissions!");

        $permissions = Permission::getValues();

        foreach ($permissions as $permission) {
            PermissionModel::firstOrCreate(['name' => $permission]);
        }

        $this->info("Permissions successfully synced!");
    }

    private function syncRolesDefaultPermissions(): void
    {
        $this->line("Syncing role default permissions!");

        $roles = RoleModel::all();
        foreach ($roles as $role) {
            $permissions = Role::getPermissions($role->name);
            if (count($permissions) > 0) {
                $this->syncRolePermissions($role, $permissions);
            }
        }

        $this->info("Default role permissions successfully synced!");
    }

    private function syncRolePermissions(RoleModel $role, array $permissions): void
    {
        if (in_array('ALL', $permissions)) {
            $allPermissions = Permission::getValues();
            $role->givePermissionTo(...$allPermissions);
        } else {
            $role->givePermissionTo(...$permissions);
        }
    }
}
