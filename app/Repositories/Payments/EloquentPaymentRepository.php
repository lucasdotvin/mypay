<?php

namespace App\Repositories\Payments;

use App\Contracts\Payments\PaymentRepository;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Pagination\CursorPaginator;

class EloquentPaymentRepository implements PaymentRepository
{
    public function getSentAndReceivedPaymentsFromUser(User $user, string $cursor = null): CursorPaginator
    {
        return Payment::with(['payee', 'payer'])
            ->whereBelongsTo($user, 'payee')
            ->orWhereBelongsTo($user, 'payer')
            ->cursorPaginate(cursor: $cursor);
    }
}
