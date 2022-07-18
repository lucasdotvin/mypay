<?php

namespace App\Repositories;

use App\Contracts\UserRepository;
use App\Models\User;
use Illuminate\Pagination\CursorPaginator;

class EloquentUserRepository implements UserRepository
{
    public function getUsersExcept(int $userId, string $column = 'created_at', string $cursor = null): CursorPaginator
    {
        return User::where('id', '!=', $userId)
            ->orderBy($column, 'asc')
            ->cursorPaginate(cursor: $cursor);
    }

    public function getDocumentById(int $id): ?string
    {
        return User::whereId($id)->value('document');
    }
}
