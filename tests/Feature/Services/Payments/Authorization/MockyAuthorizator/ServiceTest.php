<?php

namespace Tests\Feature\Services\Payments\Authorization\MockyAuthorizator;

use App\DTO\Payments\AuthorizationPayload;
use App\Services\Payments\Authorization\MockyAuthorizator\AuthorizationResult;
use App\Services\Payments\Authorization\MockyAuthorizator\Client;
use App\Services\Payments\Authorization\MockyAuthorizator\Service;
use Mockery\MockInterface;
use Tests\TestCase;

class ServiceTest extends TestCase
{
    /** @test */
    public function it_returns_true_if_the_payment_was_authorized()
    {
        $clientResponse = [
            'message' => AuthorizationResult::Authorized->value,
        ];

        $authorizationPayload = new AuthorizationPayload(
            fake()->randomNumber(),
            fake('pt_BR')->cpf(),
            fake('pt_BR')->cpf(),
        );

        $this->mock(Client::class, function (MockInterface $mock) use ($clientResponse) {
            $mock->shouldReceive('authorize')
                ->andReturn($clientResponse);
        });

        $service = app(Service::class);
        $result = $service->authorize($authorizationPayload);

        $this->assertTrue($result);
    }

    /** @test */
    public function it_returns_false_if_the_payment_was_denied()
    {
        $clientResponse = [
            'message' => AuthorizationResult::Denied->value,
        ];

        $authorizationPayload = new AuthorizationPayload(
            fake()->randomNumber(),
            fake('pt_BR')->cpf(),
            fake('pt_BR')->cpf(),
        );

        $this->mock(Client::class, function (MockInterface $mock) use ($clientResponse) {
            $mock->shouldReceive('authorize')
                ->andReturn($clientResponse);
        });

        $service = app(Service::class);
        $result = $service->authorize($authorizationPayload);

        $this->assertFalse($result);
    }
}
