<?php

namespace Tests\Feature\Http;

use App\Contracts\Payments\Authorization\AuthorizationService;
use App\Contracts\Permissions\PermissionsService;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Bus;
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

    /** @test */
    public function it_requires_an_authenticated_user_to_create_a_payment()
    {
        $response = $this->postJson(route('payments.store'));

        $response->assertUnauthorized();
    }

    /** @test */
    public function it_requires_a_payee_id_to_create_a_payment()
    {
        Sanctum::actingAs(User::factory()->createOne());

        $response = $this->postJson(route('payments.store'));

        $response->assertJsonValidationErrors('payee_id');
    }

    /** @test */
    public function it_requires_a_valid_payee_id_to_create_a_payment()
    {
        Sanctum::actingAs(User::factory()->createOne());

        $response = $this->postJson(route('payments.store'), [
            'payee_id' => -1,
        ]);

        $response->assertJsonValidationErrors('payee_id');
    }

    /** @test */
    public function it_requires_a_payee_id_different_from_the_current_user_id_to_create_a_payment()
    {
        Sanctum::actingAs($user = User::factory()->createOne());

        $response = $this->postJson(route('payments.store'), [
            'payee_id' => $user->id,
        ]);

        $response->assertJsonValidationErrors('payee_id');
    }

    /** @test */
    public function it_requires_an_amount_to_create_a_payment()
    {
        Sanctum::actingAs(User::factory()->createOne());

        $response = $this->postJson(route('payments.store'));

        $response->assertJsonValidationErrors('amount');
    }

    /** @test */
    public function it_requires_a_positive_amount_to_create_a_payment()
    {
        Sanctum::actingAs(User::factory()->createOne());

        $response = $this->postJson(route('payments.store'), [
            'amount' => -1,
        ]);

        $response->assertJsonValidationErrors('amount');
    }

    /** @test */
    public function it_requires_a_not_null_amount_to_create_a_payment()
    {
        Sanctum::actingAs(User::factory()->createOne());

        $response = $this->postJson(route('payments.store'), [
            'amount' => 0,
        ]);

        $response->assertJsonValidationErrors('amount');
    }

    /** @test */
    public function it_requires_an_affordable_amount_to_create_a_payment()
    {
        Sanctum::actingAs(User::factory()->createOne([
            'balance' => 100,
        ]));

        $response = $this->postJson(route('payments.store'), [
            'amount' => 101,
        ]);

        $response->assertJsonValidationErrors('amount');
    }

    /** @test */
    public function it_requires_an_integer_amount_to_create_a_payment()
    {
        Sanctum::actingAs(User::factory()->createOne([
            'balance' => 100,
        ]));

        $response = $this->postJson(route('payments.store'), [
            'amount' => 99.99,
        ]);

        $response->assertJsonValidationErrors('amount');
    }

    /** @test */
    public function it_requires_message_to_be_present_to_create_a_payment()
    {
        Sanctum::actingAs(User::factory()->createOne());

        $response = $this->postJson(route('payments.store'));

        $response->assertJsonValidationErrors('message');
    }

    /** @test */
    public function it_requires_message_to_be_a_string_to_create_a_payment()
    {
        Sanctum::actingAs(User::factory()->createOne());

        $response = $this->postJson(route('payments.store'), [
            'message' => 1,
        ]);

        $response->assertJsonValidationErrors('message');
    }

    /** @test */
    public function it_requires_message_to_be_a_maximum_of_255_characters_to_create_a_payment()
    {
        Sanctum::actingAs(User::factory()->createOne());

        $response = $this->postJson(route('payments.store'), [
            'message' => str_repeat('#', 256),
        ]);

        $response->assertJsonValidationErrors('message');
    }

    /** @test */
    public function it_creates_a_payment()
    {
        $payee = User::factory()->createOne();

        Sanctum::actingAs($user = User::factory()->createOne([
            'balance' => 100,
        ]));

        Bus::fake();

        $response = $this->postJson(route('payments.store'), [
            'payee_id' => $payee->id,
            'amount' => 100,
            'message' => 'Payment for something',
        ]);

        $this->assertDatabaseHas('payments', [
            'id' => $response->json('id'),
            'amount' => 100,
            'message' => 'Payment for something',
            'payer_id' => $user->id,
            'payee_id' => $payee->id,
        ]);
    }

    /** @test */
    public function it_updates_payee_balance()
    {
        $payee = User::factory()->createOne();

        Sanctum::actingAs(User::factory()->createOne([
            'balance' => 100,
        ]));

        Bus::fake();

        $this->postJson(route('payments.store'), [
            'payee_id' => $payee->id,
            'amount' => 100,
            'message' => 'Payment for something',
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $payee->id,
            'balance' => 100,
        ]);
    }

    /** @test */
    public function it_updates_payer_balance()
    {
        $payee = User::factory()->createOne();

        Sanctum::actingAs($user = User::factory()->createOne([
            'balance' => 100,
        ]));

        Bus::fake();

        $this->postJson(route('payments.store'), [
            'payee_id' => $payee->id,
            'amount' => 100,
            'message' => 'Payment for something',
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'balance' => 0,
        ]);
    }

    /** @test */
    public function it_returns_error_if_the_payment_was_not_authorized()
    {
        $payee = User::factory()->createOne();

        Sanctum::actingAs(User::factory()->createOne([
            'balance' => 100,
        ]));

        $this->mock(AuthorizationService::class, function ($mock) {
            $mock->shouldReceive('authorize')->andReturn(false);
        });

        $response = $this->postJson(route('payments.store'), [
            'payee_id' => $payee->id,
            'amount' => 100,
            'message' => 'Payment for something',
        ]);

        $response->assertStatus(500)
            ->assertJson([
                'message' => trans('exceptions.denied_payment'),
            ]);
    }

    /** @test */
    public function it_returns_error_if_the_user_cannot_do_payments()
    {
        $payee = User::factory()->createOne();

        Sanctum::actingAs(User::factory()->createOne([
            'balance' => 100,
        ]));

        $this->mock(PermissionsService::class, function ($mock) {
            $mock->shouldReceive('userCan')->andReturn(false);
        });

        $response = $this->postJson(route('payments.store'), [
            'payee_id' => $payee->id,
            'amount' => 100,
            'message' => 'Payment for something',
        ]);

        $response->assertStatus(403)
            ->assertJson([
                'message' => trans('exceptions.cant_perform_action'),
            ]);
    }
}
