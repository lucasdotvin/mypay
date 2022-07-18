<?php

namespace App\Contracts;

use App\Models\User;

interface UserService
{
    public function create(string $firstName, string $lastName, string $email, string $document, string $password, string $role): User;
}
