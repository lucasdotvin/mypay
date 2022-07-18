<?php

namespace App\Services;

use App\Contracts\UserService as UserServiceContract;
use App\Models\Role;
use App\Models\User;

class UserService implements UserServiceContract
{
    /**
     * @param  string  $role
     * @return \App\Models\User
     */
    public function create(string $firstName, string $lastName, string $email, string $document, string $password, string $role): User
    {
        $user = new User([
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email,
            'document' => $document,
            'password' => bcrypt($password),
            'balance' => 0,
        ]);

        $roleId = Role::whereSlug($role)->value('id');
        $user->role()->associate($roleId);

        $user->save();

        return $user;
    }
}
