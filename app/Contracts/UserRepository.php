<?php

namespace App\Contracts;

interface UserRepository
{
    public function getDocumentById(int $id): ?string;
}
