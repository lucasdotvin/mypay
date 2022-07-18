<?php

namespace App\Contracts\Balance;

interface BalanceService
{
    /**
     * Get the current user balance.
     *
     * @return int
     */
    public function getCurrentUserBalance(): int;

    /**
     * Get the balance of a user.
     *
     * @param int $id
     * @return int
     */
    public function getUserBalance(int $id): int;

    /**
     * Increment the balance of a user.
     *
     * @param int $id
     * @param int $amount
     * @return void
     */
    public function incrementUserBalance(int $id, int $amount): void;

    /**
     * Decrement the balance of a user.
     *
     * @param int $id
     * @param int $amount
     * @return void
     */
    public function decrementUserBalance(int $id, int $amount): void;
}
