<?php

namespace App\Services\Payments\Authorization\MockyAuthorizator;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

class Client
{
    private PendingRequest $httpClient;

    public function __construct()
    {
        $this->httpClient = $this->buildHttpClient();
    }

    public function authorize(array $payload): array
    {
        $response = $this->httpClient->post('v3/8fafdd68-a090-496f-8c9a-3442cf30dae6', $payload);

        return $response->json();
    }

    private function buildHttpClient()
    {
        return Http::baseUrl(config('services.mocky.url'));
    }
}
