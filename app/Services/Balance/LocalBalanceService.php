<?php

namespace App\Services\Balance;

use App\Contracts\Balance\BalanceService as BalanceServiceContract;
use App\Models\User;

class LocalBalanceService implements BalanceServiceContract
{
    public function getCurrentUserBalance(): int
    {
        return auth()->user()->balance;
    }

    public function getUserBalance(int $id): int
    {
        return User::whereId($id)->value('balance');
    }

    public function incrementUserBalance(int $id, int $amount): void
    {
        User::whereId($id)
            ->limit(1)
            ->increment('balance', $amount);
    }

    public function decrementUserBalance(int $id, int $amount): void
    {
        User::whereId($id)
            ->limit(1)
            ->decrement('balance', $amount);
    }
}
