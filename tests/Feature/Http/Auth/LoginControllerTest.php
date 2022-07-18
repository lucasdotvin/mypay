<?php

namespace Tests\Feature\Http\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\FillDatabaseWithMandatoryData;

class LoginControllerTest extends TestCase
{
    use FillDatabaseWithMandatoryData;
    use RefreshDatabase;

    /** @test */
    public function it_requires_an_identity_to_create_a_token()
    {
        $response = $this->postJson(route('auth.login'));

        $response->assertUnprocessable()
            ->assertJsonValidationErrors('identity');
    }

    /** @test */
    public function it_requires_an_identity_with_255_chars_at_maximum()
    {
        $response = $this->postJson(route('auth.login'), ['identity' => str_repeat('#', 256)]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors('identity');
    }

    /** @test */
    public function it_requires_a_string_identity_to_create_a_token()
    {
        $response = $this->postJson(route('auth.login'), ['identity' => 1]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors('identity');
    }

    /** @test */
    public function it_requires_email_to_create_a_token()
    {
        $response = $this->postJson(route('auth.login'));

        $response->assertUnprocessable()
            ->assertJsonValidationErrors('email');
    }

    /** @test */
    public function it_requires_a_valid_email_to_create_a_token()
    {
        $response = $this->postJson(route('auth.login'), [
            'email' => 'invalid-email',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors('email');
    }

    /** @test */
    public function it_requires_a_registered_email_to_create_a_token()
    {
        $response = $this->postJson(route('auth.login'), [
            'email' => 'not-registered@email.com',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors('email');
    }

    /** @test */
    public function it_requires_a_password_to_create_a_token()
    {
        $response = $this->postJson(route('auth.login'));

        $response->assertUnprocessable()
            ->assertJsonValidationErrors('password');
    }

    /** @test */
    public function it_requires_valid_credentials_to_create_a_token()
    {
        $user = User::factory()->createOne();

        $response = $this->postJson(route('auth.login'), [
            'email' => $user->email,
            'identity' => 'identity',
            'password' => 'invalid-password',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors('email');
    }

    /** @test */
    public function it_stores_a_token_on_the_database()
    {
        $user = User::factory()->createOne();

        $this->postJson(route('auth.login'), [
            'email' => $user->email,
            'identity' => 'identity',
            'password' => 'password',
        ]);

        $this->assertDatabaseHas('personal_access_tokens', [
            'name' => 'identity',
            'tokenable_id' => $user->id,
            'tokenable_type' => User::class,
        ]);
    }

    /** @test */
    public function it_returns_the_token()
    {
        $user = User::factory()->createOne();

        $response = $this->postJson(route('auth.login'), [
            'email' => $user->email,
            'identity' => 'identity',
            'password' => 'password',
        ]);

        $storedToken = $user->tokens()->first();

        $returnedToken = $response->json('token');
        [$returnedTokenId, $returnedTokenSecret] = explode('|', $returnedToken);

        $this->assertEquals($storedToken->token, hash('sha256', $returnedTokenSecret));
        $this->assertEquals($storedToken->id, $returnedTokenId);
    }
}
