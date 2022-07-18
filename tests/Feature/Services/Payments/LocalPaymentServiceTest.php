<?php

namespace Tests\Feature\Services\Payments;

use App\Exceptions\Payments\NonSufficientFunds;
use App\Models\User;
use App\Services\Payments\LocalPaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\FillDatabaseWithMandatoryData;
use ValueError;

class LocalPaymentServiceTest extends TestCase
{
    use RefreshDatabase;
    use FillDatabaseWithMandatoryData;

    /** @test */
    public function it_returns_true_if_the_user_can_afford_a_payment()
    {
        $this->actingAs(User::factory()->createOne(['balance' => 100]));

        $service = app(LocalPaymentService::class);
        $result = $service->canAfford(99);

        $this->assertTrue($result);
    }

    /** @test */
    public function it_returns_false_if_the_user_cannot_afford_a_payment()
    {
        $this->actingAs(User::factory()->createOne(['balance' => 100]));

        $service = app(LocalPaymentService::class);
        $result = $service->canAfford(101);

        $this->assertFalse($result);
    }

    /** @test */
    public function it_creates_a_payment()
    {
        $payee = User::factory()->createOne();

        $this->actingAs($payer = User::factory()->createOne(['balance' => 100]));

        $service = app(LocalPaymentService::class);
        $paymmentId = $service->pay(99, 'Lorem ipsum.', $payee->id);

        $this->assertDatabaseHas('payments', [
            'id' => $paymmentId,
            'payee_id' => $payee->id,
            'payer_id' => $payer->id,
            'amount' => 99,
            'message' => 'Lorem ipsum.',
        ]);
    }

    /** @test */
    public function it_updates_payee_balance()
    {
        $payee = User::factory()->createOne();

        $this->actingAs(User::factory()->createOne(['balance' => 100]));

        $service = app(LocalPaymentService::class);
        $service->pay(99, 'Lorem ipsum.', $payee->id);

        $this->assertDatabaseHas('users', [
            'id' => $payee->id,
            'balance' => 99,
        ]);
    }

    /** @test */
    public function it_updates_payer_balance()
    {
        $payee = User::factory()->createOne();

        $this->actingAs($payer = User::factory()->createOne(['balance' => 100]));

        $service = app(LocalPaymentService::class);
        $service->pay(99, 'Lorem ipsum.', $payee->id);

        $this->assertDatabaseHas('users', [
            'id' => $payer->id,
            'balance' => 1,
        ]);
    }

    /** @test */
    public function it_raises_an_exception_if_the_user_cannot_afford_payment()
    {
        $payee = User::factory()->createOne();

        $this->actingAs($payer = User::factory()->createOne(['balance' => 100]));

        $this->expectException(NonSufficientFunds::class);

        $service = app(LocalPaymentService::class);
        $service->pay(101, 'Lorem ipsum.', $payee->id);

        $this->assertDatabaseHas('users', [
            'id' => $payer->id,
            'balance' => 100,
        ]);

        $this->assertDatabaseCount('payments', 0);
    }
}
