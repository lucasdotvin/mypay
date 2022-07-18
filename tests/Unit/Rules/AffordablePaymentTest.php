<?php

namespace Tests\Unit\Rules;

use App\Contracts\Payments\PaymentService;
use App\Rules\AffordablePayment;
use Mockery\MockInterface;
use Tests\TestCase;

class AffordablePaymentTest extends TestCase
{
    /** @test */
    public function it_returns_true_for_affordable_payments()
    {
        $this->mock(PaymentService::class, function (MockInterface $mock) {
            $mock->shouldReceive('canAfford')
                ->once()
                ->andReturn(true);
        });

        $rule = new AffordablePayment;
        $passes = $rule->passes('amount', 99);

        $this->assertTrue($passes);
    }

    /** @test */
    public function it_returns_false_for_not_affordable_payments()
    {
        $this->mock(PaymentService::class, function (MockInterface $mock) {
            $mock->shouldReceive('canAfford')
                ->once()
                ->andReturn(false);
        });

        $rule = new AffordablePayment;
        $passes = $rule->passes('amount', 101);

        $this->assertFalse($passes);
    }

    /** @test */
    public function it_returns_the_expected_error_message()
    {
        $rule = new AffordablePayment;

        $message = $rule->message();

        $this->assertEquals(trans('validation.affordable_payment'), $message);
    }
}
