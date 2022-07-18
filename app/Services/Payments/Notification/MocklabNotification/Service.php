<?php

namespace App\Services\Payments\Notification\MocklabNotification;

use App\Contracts\Payments\Notification\NotificationService as NotificationServiceContract;
use App\Exceptions\Payments\NotificationNotSent;

class Service implements NotificationServiceContract
{
    public function __construct(
        private Client $client,
    ) {
    }

    public function notify(int $userId, int $paymentId): void
    {
        $requestPayload = [
            'user_id' => $userId,
            'payment_id' => $paymentId,
        ];

        $apiResponse = $this->client->sendNotification($requestPayload);

        throw_if($apiResponse['message'] !== NotificationResult::Success->value, new NotificationNotSent);
    }
}
