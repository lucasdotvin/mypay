<?php

namespace App\Contracts\Payments;

use App\Exceptions\Payments\DeniedPayment;
use App\Exceptions\Payments\NonSufficientFunds;
use Illuminate\Contracts\Pagination\CursorPaginator;

interface PaymentService
{
    /**
     * Get all payments sent by the user.
     *
     * @return CursorPaginator
     */
    public function getMySentAndReceivedPayments(string $cursor = null): CursorPaginator;

    /**
     * Pay an amount to a user.
     *
     * @param  int  $amount
     * @param  string  $message
     * @param  int  $payeeId
     * @return int
     *
     * @throws DeniedPayment if the payment was not authorized.
     * @throws NonSufficientFunds if the user cannot afford the amount.
     */
    public function pay(int $amount, string $message, int $payeeId): int;

    /**
     * Check if the user can afford the amount.
     *
     * @param  int  $amount
     * @return bool
     */
    public function canAfford(int $amount): bool;
}
