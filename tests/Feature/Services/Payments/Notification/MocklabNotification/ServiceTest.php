<?php

namespace Tests\Feature\Services\Payments\Notification\MocklabNotification;

use App\Exceptions\Payments\NotificationNotSent;
use App\Services\Payments\Notification\MocklabNotification\NotificationResult;
use App\Services\Payments\Notification\MocklabNotification\Client;
use App\Services\Payments\Notification\MocklabNotification\Service;
use Mockery\MockInterface;
use Tests\TestCase;

class ServiceTest extends TestCase
{
    /** @test */
    public function it_runs_properly_if_the_notification_was_sent()
    {
        $clientResponse = [
            'message' => NotificationResult::Success->value,
        ];

        $this->mock(Client::class, function (MockInterface $mock) use ($clientResponse) {
            $mock->shouldReceive('sendNotification')
                ->andReturn($clientResponse);
        });

        $service = app(Service::class);
        $service->notify(1, 1);

        $this->assertTrue(true);
    }

    /** @test */
    public function it_throws_exception_if_the_notification_was_not_sent()
    {
        $clientResponse = [
            'message' => NotificationResult::Fail->value,
        ];

        $this->mock(Client::class, function (MockInterface $mock) use ($clientResponse) {
            $mock->shouldReceive('sendNotification')
                ->andReturn($clientResponse);
        });

        $this->expectException(NotificationNotSent::class);

        $service = app(Service::class);
        $service->notify(1, 1);
    }
}
