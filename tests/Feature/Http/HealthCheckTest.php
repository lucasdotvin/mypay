<?php

namespace Tests\Feature\Http;

use Tests\TestCase;

class HealthCheckTest extends TestCase
{
    /** @test */
    public function it_returns_a_200_status_code_with_a_body_containing_the_app_version()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertJson(['Laravel' => app()->version()]);
    }
}
