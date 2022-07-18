<?php

namespace App\Services\Payments\Notification\MocklabNotification;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

class Client
{
    private PendingRequest $httpClient;

    public function __construct()
    {
        $this->httpClient = $this->buildHttpClient();
    }

    public function sendNotification(array $payload): array
    {
        $response = $this->httpClient->post('notify', $payload);

        return $response->json();
    }

    private function buildHttpClient()
    {
        return Http::baseUrl(config('services.mocklab.url'));
    }
}
