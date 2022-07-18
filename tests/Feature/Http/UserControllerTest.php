<?php

namespace Tests\Feature\Http;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use Tests\Traits\FillDatabaseWithMandatoryData;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;
    use FillDatabaseWithMandatoryData;

    /** @test */
    public function it_requires_an_authenticated_user_to_list_users()
    {
        $response = $this->getJson(route('users.index'));

        $response->assertUnauthorized();
    }

    /** @test */
    public function it_lists_the_stored_users()
    {
        Sanctum::actingAs(User::factory()->createOne());

        User::factory()->createOne(['first_name' => 'cd']);
        $firstUser = User::factory()->createOne(['first_name' => 'ab']);

        $response = $this->getJson(route('users.index'));

        $response->assertOk()
            ->assertJson(fn (AssertableJson $json) => $json->hasAll(['meta', 'links'])
                    ->has('users', 2)
                    ->has('users.0', fn (AssertableJson $json) => $json->where('id', $firstUser->id)
                            ->where('first_name', $firstUser->first_name)
                            ->where('last_name', $firstUser->last_name)
                            ->has('role')
                    )
            );
    }
}
