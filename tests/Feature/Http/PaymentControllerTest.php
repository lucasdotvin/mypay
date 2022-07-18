<?php

namespace Tests\Feature\Http;

use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use Tests\Traits\FillDatabaseWithMandatoryData;

class PaymentControllerTest extends TestCase
{
    use RefreshDatabase;
    use FillDatabaseWithMandatoryData;
    use WithFaker;

    /** @test */
    public function it_requires_an_authenticated_user_to_list_payments()
    {
        $response = $this->getJson(route('payments.index'));

        $response->assertUnauthorized();
    }

    /** @test */
    public function it_lists_the_stored_payments()
    {
        Sanctum::actingAs($user = User::factory()->createOne());

        $sentPayment = Payment::factory()
            ->for($user, 'payer')
            ->createOne();

        $receivedPayment = Payment::factory()
            ->for($user, 'payee')
            ->createOne();

        $response = $this->getJson(route('payments.index'));

        $response->assertOk()
            ->assertJson(fn (AssertableJson $json) => $json->hasAll(['meta', 'links'])
                    ->has('payments', 2)
                    ->has('payments.0', fn (AssertableJson $json) => $json->where('id', $sentPayment->id)
                            ->where('amount', $sentPayment->amount)
                            ->where('message', $sentPayment->message)
                            ->where('created_at', $sentPayment->created_at->toIsoString())
                            ->where('payer.id', $user->id)
                            ->where('payer.first_name', $user->first_name)
                            ->where('payer.last_name', $user->last_name)
                            ->has('payee')
                    )
                    ->has('payments.1', fn (AssertableJson $json) => $json->where('id', $receivedPayment->id)
                            ->where('amount', $receivedPayment->amount)
                            ->where('message', $receivedPayment->message)
                            ->where('created_at', $receivedPayment->created_at->toIsoString())
                            ->where('payee.id', $user->id)
                            ->where('payee.first_name', $user->first_name)
                            ->where('payee.last_name', $user->last_name)
                            ->has('payer')
                    )
            );
    }
}
