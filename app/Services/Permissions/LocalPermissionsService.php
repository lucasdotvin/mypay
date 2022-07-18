<?php

namespace App\Services\Permissions;

use App\Contracts\Permissions\PermissionsService as PermissionsServiceContract;
use App\Models\Role;

class LocalPermissionsService implements PermissionsServiceContract
{
    public function userCan(int $userId, string $permissionSlug): bool
    {
        $role = Role::whereRelation('users', 'id', $userId)->first();

        $permissions = $role->permissions()->get();

        return $permissions->contains('slug', $permissionSlug);
    }
}
