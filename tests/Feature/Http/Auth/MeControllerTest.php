<?php

namespace Tests\Feature\Http\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use Tests\Traits\FillDatabaseWithMandatoryData;

class MeControllerTest extends TestCase
{
    use FillDatabaseWithMandatoryData;
    use RefreshDatabase;

    /** @test */
    public function it_requires_an_authenticated_user_to_list_payments()
    {
        $response = $this->getJson(route('auth.me'));

        $response->assertUnauthorized();
    }

    /** @test */
    public function it_returns_user_data()
    {
        Sanctum::actingAs($user = User::factory()->createOne());

        $response = $this->getJson(route('auth.me'));

        $response->assertOk()
            ->assertJson(fn (AssertableJson $json) => $json->where('id', $user->id)
                    ->where('first_name', $user->first_name)
                    ->where('last_name', $user->last_name)
                    ->where('email', $user->email)
                    ->where('document', $user->document)
                    ->where('balance', $user->balance)
                    ->where('role.slug', $user->role->slug)
                    ->has('created_at')
            );
    }
}
