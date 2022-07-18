<?php

namespace Tests\Feature\Services\Payments\Notification\MocklabNotification;

use App\Services\Payments\Notification\MocklabNotification\Client;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ClientTest extends TestCase
{
    /** @test */
    public function it_sends_user_data()
    {
        $result = [
            'message' => 'Success',
        ];

        Http::fake([
            '*' => Http::response($result),
        ]);

        $service = new Client;
        $receivedData = $service->sendNotification(['foo' => 'bar']);

        $this->assertEquals($result, $receivedData);

        Http::assertSequencesAreEmpty();
    }

    /** @test */
    public function it_sends_authorization_payload()
    {
        $payload = [
            'user_id' => 1,
        ];

        Http::fake([
            '*' => Http::response([]),
        ]);

        $service = new Client;
        $service->sendNotification($payload);

        Http::assertSent(fn (Request $request) => $request->body() === json_encode($payload));
    }
}
