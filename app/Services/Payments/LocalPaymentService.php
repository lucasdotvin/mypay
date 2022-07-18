<?php

namespace App\Services\Payments;

use App\Contracts\Balance\BalanceService;
use App\Contracts\Payments\Authorization\AuthorizationService;
use App\Contracts\Payments\PaymentRepository;
use App\Contracts\Payments\PaymentService as PaymentServiceContract;
use App\Contracts\Permissions\PermissionsService;
use App\Contracts\UserRepository;
use App\DTO\Payments\AuthorizationPayload;
use App\Exceptions\Payments\DeniedPayment;
use App\Exceptions\Payments\NonSufficientFunds;
use App\Exceptions\Permissions\CantPerformAction;
use App\Models\Payment;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Support\Facades\DB;

class LocalPaymentService implements PaymentServiceContract
{
    public function __construct(
        private AuthorizationService $authorizationService,
        private BalanceService $balanceService,
        private PermissionsService $permissionsService,
        private PaymentRepository $paymentRepository,
        private UserRepository $userRepository,
    ) {
    }

    public function getMySentAndReceivedPayments(string $cursor = null): CursorPaginator
    {
        return $this->paymentRepository->getSentAndReceivedPaymentsFromUser(auth()->user(), $cursor);
    }

    public function pay(int $amount, string $message, int $payeeId): int
    {
        return DB::transaction(function () use ($amount, $message, $payeeId) {
            throw_unless($this->canAfford($amount), new NonSufficientFunds);

            $this->checkUserPermissions();

            $this->authorizePayment($amount, $payeeId, auth()->id());

            $payment = $this->registerPayment($amount, $message, $payeeId, auth()->id());

            return $payment->id;
        });
    }

    public function canAfford(int $amount): bool
    {
        return $this->balanceService->getCurrentUserBalance() >= $amount;
    }

    /**
     * Check if the user can do payments.
     *
     * @throws DeniedPayment if the payment was not authorized.
     */
    private function checkUserPermissions()
    {
        $hasPermissions = $this->permissionsService->userCan(auth()->id(), 'pay');

        throw_unless($hasPermissions, new CantPerformAction);
    }

    /**
     * Authorize a payment.
     *
     * @param int $amount
     * @param int $payeeId
     * @param int $payerId
     * @return Payment
     *
     * @throws DeniedPayment if the payment was not authorized.
     */
    private function authorizePayment(int $amount, int $payeeId, int $payerId)
    {
        $authorizationPayload = new AuthorizationPayload(
            $amount,
            $this->userRepository->getDocumentById($payeeId),
            $this->userRepository->getDocumentById($payerId),
        );

        $authorized = $this->authorizationService->authorize($authorizationPayload);

        throw_unless($authorized, new DeniedPayment);
    }

    /**
     * Create a payment register and update the users' balances.
     *
     * @param  int  $amount
     * @param  string  $message
     * @param  int  $payeeId
     * @param  int  $payerId
     * @return Payment
     */
    private function registerPayment(int $amount, string $message, int $payeeId, int $payerId): Payment
    {
        return DB::transaction(function () use ($amount, $message, $payeeId, $payerId) {
            $payment = new Payment(['amount' => $amount, 'message' => $message]);

            $payment->payee()
                ->associate($payeeId);

            $payment->payer()
                ->associate($payerId);

            $payment->save();

            $this->balanceService
                ->incrementUserBalance($payeeId, $amount);

            $this->balanceService
                ->decrementUserBalance($payerId, $amount);

            return $payment;
        });
    }
}
