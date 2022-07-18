<?php

namespace Tests\Unit\Rules\Documents;

use App\Rules\Documents\Cpf;
use PHPUnit\Framework\TestCase;

class CpfTest extends TestCase
{
    /** @test */
    public function it_returns_true_for_formatted_valid_cpfs()
    {
        $rule = new Cpf;

        $passes = $rule->passes('cpf', '264.260.080-79');

        $this->assertTrue($passes);
    }

    /** @test */
    public function it_returns_true_for_unformatted_valid_cpfs()
    {
        $rule = new Cpf;

        $passes = $rule->passes('cpf', '26426008079');

        $this->assertTrue($passes);
    }

    /** @test */
    public function it_returns_false_for_formatted_invalid_cpfs()
    {
        $rule = new Cpf;

        $passes = $rule->passes('cpf', '264.260.080-80');

        $this->assertFalse($passes);
    }

    /** @test */
    public function it_returns_false_for_unformatted_invalid_cpfs()
    {
        $rule = new Cpf;

        $passes = $rule->passes('cpf', '26426008080');

        $this->assertFalse($passes);
    }

    /** @test */
    public function it_returns_false_for_formatted_cpfs_with_repeated_digits()
    {
        $rule = new Cpf;

        $passes = $rule->passes('cpf', '111.111.111-11');

        $this->assertFalse($passes);
    }

    /** @test */
    public function it_returns_false_for_unformatted_cpfs_with_repeated_digits()
    {
        $rule = new Cpf;

        $passes = $rule->passes('cpf', '11111111111');

        $this->assertFalse($passes);
    }

    /** @test */
    public function it_returns_the_expected_error_message()
    {
        $rule = new Cpf;

        $message = $rule->message();

        $this->assertEquals('validation.cpf', $message);
    }
}
