<?php

namespace App\DTO\Payments;

class AuthorizationPayload
{
    public function __construct(
        public int $amount,
        public string $payerDocument,
        public string $payeeDocument,
    ) {
    }
}
