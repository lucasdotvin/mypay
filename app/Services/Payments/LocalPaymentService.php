<?php

namespace App\Services\Payments;

use App\Contracts\Balance\BalanceService;
use App\Contracts\Payments\PaymentRepository;
use App\Contracts\Payments\PaymentService as PaymentServiceContract;
use App\Models\Payment;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Support\Facades\DB;
use ValueError;

class LocalPaymentService implements PaymentServiceContract
{
    public function __construct(
        private BalanceService $balanceService,
        private PaymentRepository $paymentRepository,
    ) {
    }

    public function getMySentAndReceivedPayments(string $cursor = null): CursorPaginator
    {
        return $this->paymentRepository->getSentAndReceivedPaymentsFromUser(auth()->user(), $cursor);
    }

    public function pay(int $amount, string $message, int $payeeId): int
    {
        return DB::transaction(function () use ($amount, $message, $payeeId) {
            if (! $this->canAfford($amount)) {
                throw new ValueError(trans('exceptions.messages.non-sufficient-funds'));
            }

            $payment = $this->registerPayment($amount, $message, $payeeId, auth()->id());

            $this->balanceService
                ->incrementUserBalance($payeeId, $amount);

            $this->balanceService
                ->decrementUserBalance(auth()->id(), $amount);

            return $payment->id;
        });
    }

    public function canAfford(int $amount): bool
    {
        return $this->balanceService->getCurrentUserBalance() >= $amount;
    }

    /**
     * Create a payment register.
     *
     * @param  int  $amount
     * @param  string  $message
     * @param  int  $payeeId
     * @param  int  $payerId
     * @return Payment
     */
    private function registerPayment(int $amount, string $message, int $payeeId, int $payerId): Payment
    {
        $payment = new Payment(['amount' => $amount, 'message' => $message]);

        $payment->payee()
            ->associate($payeeId);

        $payment->payer()
            ->associate($payerId);

        $payment->save();

        return $payment;
    }
}
