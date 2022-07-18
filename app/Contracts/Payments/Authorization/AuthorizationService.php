<?php

namespace App\Contracts\Payments\Authorization;

use App\DTO\Payments\AuthorizationPayload;

interface AuthorizationService
{
    /**
     * Check if the payment was authorized. Returns true if the payment was authorized, false otherwise.
     *
     * @param AuthorizationPayload $authorizationPayload
     * @return bool
     */
    public function authorize(AuthorizationPayload $payload): bool;
}
