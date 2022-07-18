<?php

namespace Tests\Feature\Services\Payments\Authorization\MockyAuthorizator;

use App\Services\Payments\Authorization\MockyAuthorizator\Client;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ClientTest extends TestCase
{
    /** @test */
    public function it_gets_payment_authorization_result()
    {
        $result = [
            'message' => 'Autorizado',
        ];

        Http::fake([
            '*' => Http::response($result),
        ]);

        $service = new Client;
        $receivedData = $service->authorize([]);

        $this->assertEquals($result, $receivedData);

        Http::assertSequencesAreEmpty();
    }

    /** @test */
    public function it_sends_authorization_payload()
    {
        $authorizationPayload = [
            'amount' => fake()->randomNumber(),
            'payer_document' => fake('pt_BR')->cpf(),
            'payee_document' => fake('pt_BR')->cpf(),
        ];

        Http::fake([
            '*' => Http::response([]),
        ]);

        $service = new Client;
        $service->authorize($authorizationPayload);

        Http::assertSent(fn (Request $request) =>
            $request->body() === json_encode($authorizationPayload)
        );
    }
}
