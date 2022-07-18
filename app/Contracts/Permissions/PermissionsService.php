<?php

namespace App\Contracts\Permissions;

interface PermissionsService
{
    /**
     * Check if the user can perform the action.
     *
     * @param  int  $userId
     * @param  string  $permissionSlug
     * @return bool
     */
    public function userCan(int $userId, string $permissionSlug): bool;
}
