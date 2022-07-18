<?php

namespace Tests\Unit\Rules\Documents;

use App\Rules\Documents\Cnpj;
use PHPUnit\Framework\TestCase;

class CnpjTest extends TestCase
{
    /** @test */
    public function it_returns_true_for_formatted_valid_cnpjs()
    {
        $rule = new Cnpj;

        $passes = $rule->passes('cnpj', '26.941.590/0001-40');

        $this->assertTrue($passes);
    }

    /** @test */
    public function it_returns_true_for_unformatted_valid_cnpjs()
    {
        $rule = new Cnpj;

        $passes = $rule->passes('cnpj', '26941590000140');

        $this->assertTrue($passes);
    }

    /** @test */
    public function it_returns_false_for_formatted_invalid_cnpjs()
    {
        $rule = new Cnpj;

        $passes = $rule->passes('cnpj', '75.417.309/0001-81');

        $this->assertFalse($passes);
    }

    /** @test */
    public function it_returns_false_for_unformatted_invalid_cnpjs()
    {
        $rule = new Cnpj;

        $passes = $rule->passes('cnpj', '75417309000181');

        $this->assertFalse($passes);
    }

    /** @test */
    public function it_returns_false_for_formatted_cnpjs_with_repeated_digits()
    {
        $rule = new Cnpj;

        $passes = $rule->passes('cnpj', '11.111.111/1111-11');

        $this->assertFalse($passes);
    }

    /** @test */
    public function it_returns_false_for_unformatted_cnpjs_with_repeated_digits()
    {
        $rule = new Cnpj;

        $passes = $rule->passes('cnpj', '11111111111111');

        $this->assertFalse($passes);
    }

    /** @test */
    public function it_returns_the_expected_error_message()
    {
        $rule = new Cnpj;

        $message = $rule->message();

        $this->assertEquals('validation.cnpj', $message);
    }
}
