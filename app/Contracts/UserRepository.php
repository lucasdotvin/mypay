<?php

namespace App\Contracts;

use Illuminate\Contracts\Pagination\CursorPaginator;

interface UserRepository
{
    public function getUsersExcept(int $exceptionId, string $orderingColumn = 'created_at', string $cursor = null): CursorPaginator;

    public function getDocumentById(int $id): ?string;
}
