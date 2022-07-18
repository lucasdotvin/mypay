<?php

namespace App\Repositories;

use App\Contracts\UserRepository;
use App\Models\User;

class EloquentUserRepository implements UserRepository
{
    public function getDocumentById(int $id): ?string
    {
        return User::whereId($id)->value('document');
    }
}
