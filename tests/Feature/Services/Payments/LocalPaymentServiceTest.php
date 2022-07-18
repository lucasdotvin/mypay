<?php

namespace Tests\Feature\Services\Payments;

use App\Contracts\Payments\Authorization\AuthorizationService;
use App\Contracts\Permissions\PermissionsService;
use App\Exceptions\Payments\DeniedPayment;
use App\Exceptions\Payments\NonSufficientFunds;
use App\Exceptions\Permissions\CantPerformAction;
use App\Models\User;
use App\Services\Payments\LocalPaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\FillDatabaseWithMandatoryData;

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

    /** @test */
    public function it_raises_an_exception_if_the_payment_is_denied()
    {
        $payee = User::factory()->createOne();

        $this->actingAs($payer = User::factory()->createOne(['balance' => 100]));

        $this->mock(AuthorizationService::class, function ($mock) {
            $mock->shouldReceive('authorize')->andReturn(false);
        });

        $this->expectException(DeniedPayment::class);

        $service = app(LocalPaymentService::class);
        $service->pay(99, 'Lorem ipsum.', $payee->id);

        $this->assertDatabaseHas('users', [
            'id' => $payer->id,
            'balance' => 100,
        ]);

        $this->assertDatabaseCount('payments', 0);
    }

    /** @test */
    public function it_raises_an_exception_if_the_user_does_not_has_permission_to_do_payments()
    {
        $payee = User::factory()->createOne();

        $this->actingAs($payer = User::factory()->createOne(['balance' => 100]));

        $this->mock(PermissionsService::class, function ($mock) {
            $mock->shouldReceive('userCan')->andReturn(false);
        });

        $this->expectException(CantPerformAction::class);

        $service = app(LocalPaymentService::class);
        $service->pay(99, 'Lorem ipsum.', $payee->id);

        $this->assertDatabaseHas('users', [
            'id' => $payer->id,
            'balance' => 100,
        ]);

        $this->assertDatabaseCount('payments', 0);
    }
}
