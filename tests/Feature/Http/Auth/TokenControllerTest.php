<?php

namespace Tests\Feature\Http\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TokenControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_requires_an_identity_to_create_a_token()
    {
        $response = $this->postJson(route('auth.tokens.store'));

        $response->assertUnprocessable()
            ->assertJsonValidationErrors('identity');
    }

    /** @test */
    public function it_requires_an_identity_with_255_chars_at_maximum()
    {
        $response = $this->postJson(route('auth.tokens.store'), ['identity' => str_repeat('#', 256)]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors('identity');
    }

    /** @test */
    public function it_requires_a_string_identity_to_create_a_token()
    {
        $response = $this->postJson(route('auth.tokens.store'), ['identity' => 1]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors('identity');
    }

    /** @test */
    public function it_requires_email_to_create_a_token()
    {
        $response = $this->postJson(route('auth.tokens.store'));

        $response->assertUnprocessable()
            ->assertJsonValidationErrors('email');
    }

    /** @test */
    public function it_requires_a_valid_email_to_create_a_token()
    {
        $response = $this->postJson(route('auth.tokens.store'), [
            'email' => 'invalid-email',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors('email');
    }

    /** @test */
    public function it_requires_a_registered_email_to_create_a_token()
    {
        $response = $this->postJson(route('auth.tokens.store'), [
            'email' => 'not-registered@email.com',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors('email');
    }

    /** @test */
    public function it_requires_a_password_to_create_a_token()
    {
        $response = $this->postJson(route('auth.tokens.store'));

        $response->assertUnprocessable()
            ->assertJsonValidationErrors('password');
    }

    /** @test */
    public function it_requires_valid_credentials_to_create_a_token()
    {
        $user = User::factory()->createOne();

        $response = $this->postJson(route('auth.tokens.store'), [
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

        $this->postJson(route('auth.tokens.store'), [
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

        $response = $this->postJson(route('auth.tokens.store'), [
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

    /** @test */
    public function it_requires_an_authenticated_user_to_list_tokens()
    {
        $response = $this->getJson(route('auth.tokens.index'));

        $response->assertUnauthorized();
    }

    /** @test */
    public function it_lists_the_stored_tokens()
    {
        Sanctum::actingAs($user = User::factory()->createOne());

        $firstToken = $user->createToken('first-token');
        $user->createToken('second-token');

        $response = $this->getJson(route('auth.tokens.index'));

        $response->assertOk()
            ->assertJson(fn (AssertableJson $json) =>
                $json->hasAll(['meta', 'links'])
                    ->has('tokens', 2)
                    ->has('tokens.0', fn (AssertableJson $json) =>
                        $json->where('id', $firstToken->accessToken->id)
                            ->where('name', 'first-token')
                            ->where('created_at', $firstToken->accessToken->created_at->toIsoString())
                    )
            );
    }

    /** @test */
    public function it_requires_an_authenticated_user_to_delete_a_token()
    {
        $response = $this->deleteJson(route('auth.tokens.destroy', 1));

        $response->assertUnauthorized();
    }

    /** @test */
    public function it_requires_a_valid_token_to_delete_a_token()
    {
        Sanctum::actingAs($user = User::factory()->createOne());

        $response = $this->deleteJson(route('auth.tokens.destroy', 1));

        $response->assertNotFound();
    }

    /** @test */
    public function it_deletes_a_token()
    {
        Sanctum::actingAs($user = User::factory()->createOne());

        $token = $user->createToken('token');

        $this->deleteJson(route('auth.tokens.destroy', $token->accessToken->id));

        $this->assertDatabaseMissing('personal_access_tokens', [
            'id' => $token->accessToken->id,
        ]);
    }

    /** @test */
    public function it_returns_no_content_response_after_deleting_a_token()
    {
        Sanctum::actingAs($user = User::factory()->createOne());

        $token = $user->createToken('token');

        $response = $this->deleteJson(route('auth.tokens.destroy', $token->accessToken->id));

        $response->assertNoContent();
    }
}
