<?php

namespace App\Contracts\Payments;

use App\Models\Payment;
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

    /**
     * Find a payment by its ID, throw an exception if not found.
     *
     * @param  int  $id
     * @param  array  $relations
     * @return Payment
     * @throws \Illuminate\Database\Exceptions\ModelNotFoundException
     */
    public function findOrFail(int $id, array $relations = []): Payment;
}
