<?php

namespace App\Services\Payments\Authorization\MockyAuthorizator;

use App\Contracts\Payments\Authorization\AuthorizationService as AuthorizationServiceContract;
use App\DTO\Payments\AuthorizationPayload;

class Service implements AuthorizationServiceContract
{
    public function __construct(
        private Client $client,
    ) {
    }

    public function authorize(AuthorizationPayload $payload): bool
    {
        $requestPayload = [
            'amount' => $payload->amount,
            'payer_document' => $payload->payerDocument,
            'payee_document' => $payload->payeeDocument,
        ];

        $apiResponse = $this->client->authorize($requestPayload);

        return $apiResponse['message'] === AuthorizationResult::Authorized->value;
    }
}
