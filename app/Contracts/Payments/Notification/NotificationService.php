<?php

namespace App\Contracts\Payments\Notification;

interface NotificationService
{
    /**
     * Notify user about payment.
     *
     * @param  int  $userId
     * @param  int  $paymentId
     * @return void
     *
     * @throws NotificationNotSent if notification was not sent
     */
    public function notify(int $userId, int $paymentId): void;
}
