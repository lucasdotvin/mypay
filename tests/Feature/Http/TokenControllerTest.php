<?php

namespace Tests\Feature\Http;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use Tests\Traits\FillDatabaseWithMandatoryData;

class TokenControllerTest extends TestCase
{
    use RefreshDatabase;
    use FillDatabaseWithMandatoryData;

    /** @test */
    public function it_requires_an_authenticated_user_to_list_tokens()
    {
        $response = $this->getJson(route('tokens.index'));

        $response->assertUnauthorized();
    }

    /** @test */
    public function it_lists_the_stored_tokens()
    {
        Sanctum::actingAs($user = User::factory()->createOne());

        $firstToken = $user->createToken('first-token');
        $user->createToken('second-token');

        $response = $this->getJson(route('tokens.index'));

        $response->assertOk()
            ->assertJson(fn (AssertableJson $json) => $json->hasAll(['meta', 'links'])
                    ->has('tokens', 2)
                    ->has('tokens.0', fn (AssertableJson $json) => $json->where('id', $firstToken->accessToken->id)
                            ->where('name', 'first-token')
                            ->where('created_at', $firstToken->accessToken->created_at->toIsoString())
                    )
            );
    }

    /** @test */
    public function it_requires_an_authenticated_user_to_delete_a_token()
    {
        $response = $this->deleteJson(route('tokens.destroy', 1));

        $response->assertUnauthorized();
    }

    /** @test */
    public function it_requires_a_valid_token_to_delete_a_token()
    {
        Sanctum::actingAs(User::factory()->createOne());

        $response = $this->deleteJson(route('tokens.destroy', 1));

        $response->assertNotFound();
    }

    /** @test */
    public function it_deletes_a_token()
    {
        Sanctum::actingAs($user = User::factory()->createOne());

        $token = $user->createToken('token');

        $this->deleteJson(route('tokens.destroy', $token->accessToken->id));

        $this->assertDatabaseMissing('personal_access_tokens', [
            'id' => $token->accessToken->id,
        ]);
    }

    /** @test */
    public function it_returns_no_content_response_after_deleting_a_token()
    {
        Sanctum::actingAs($user = User::factory()->createOne());

        $token = $user->createToken('token');

        $response = $this->deleteJson(route('tokens.destroy', $token->accessToken->id));

        $response->assertNoContent();
    }
}
