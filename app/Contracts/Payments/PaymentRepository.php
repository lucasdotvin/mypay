<?php

namespace App\Contracts\Payments;

use App\Models\User;
use Illuminate\Contracts\Pagination\CursorPaginator;

interface PaymentRepository
{
    /**
     * Get all payments sent or received by the user.
     *
     * @return CursorPaginator
     */
    public function getSentAndReceivedPaymentsFromUser(User $user, string $cursor = null): CursorPaginator;
}
