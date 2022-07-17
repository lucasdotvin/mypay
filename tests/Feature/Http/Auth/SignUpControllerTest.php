<?php

namespace Tests\Feature\Http\Auth;

use App\Enums;
use App\Models;
use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Notification;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;
use Tests\Traits\FillDatabaseWithMandatoryData;

class SignUpControllerTest extends TestCase
{
    use RefreshDatabase;
    use FillDatabaseWithMandatoryData;
    use WithFaker;

    /** @test */
    public function it_requires_a_first_name_to_create_a_user()
    {
        $response = $this->postJson(route('auth.signup'));

        $response->assertUnprocessable()
            ->assertJsonValidationErrors('first_name');
    }

    /** @test */
    public function it_requires_a_first_name_with_at_least_3_chars_to_create_a_user()
    {
        $response = $this->postJson(route('auth.signup'), [
            'first_name' => 'ab',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors('first_name');
    }

    /** @test */
    public function it_requires_a_last_name_to_create_a_user()
    {
        $response = $this->postJson(route('auth.signup'));

        $response->assertUnprocessable()
            ->assertJsonValidationErrors('last_name');
    }

    /** @test */
    public function it_requires_a_last_name_with_at_least_3_chars_to_create_a_user()
    {
        $response = $this->postJson(route('auth.signup'), [
            'last_name' => 'ab',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors('last_name');
    }

    /** @test */
    public function it_requires_an_email_to_create_a_user()
    {
        $response = $this->postJson(route('auth.signup'));

        $response->assertUnprocessable()
            ->assertJsonValidationErrors('email');
    }

    /** @test */
    public function it_requires_a_valid_email_to_create_a_user()
    {
        $response = $this->postJson(route('auth.signup'), [
            'email' => 'invalid-email',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors('email');
    }

    /** @test */
    public function it_requires_a_not_registered_email_to_create_a_user()
    {
        User::factory()->create(['email' => 'registered@email.com']);

        $response = $this->postJson(route('auth.signup'), [
            'email' => 'registered@email.com',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors('email');
    }

    /** @test */
    public function it_requires_a_password_to_create_a_user()
    {
        $response = $this->postJson(route('auth.signup'));

        $response->assertUnprocessable()
            ->assertJsonValidationErrors('password');
    }

    /** @test */
    public function it_requires_a_password_with_at_least_6_chars_to_create_a_user()
    {
        $response = $this->postJson(route('auth.signup'), [
            'password' => $this->faker()->password(5, 5),
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors('password');
    }

    /** @test */
    public function it_requires_a_password_with_at_least_one_letter_to_create_a_user()
    {
        $response = $this->postJson(route('auth.signup'), [
            'password' => '123456',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors('password');
    }

    /** @test */
    public function it_requires_a_password_with_at_least_one_number_to_create_a_user()
    {
        $response = $this->postJson(route('auth.signup'), [
            'password' => 'abcdef',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors('password');
    }

    /** @test */
    public function it_requires_a_password_confirmation_to_create_a_user()
    {
        $response = $this->postJson(route('auth.signup'), [
            'password' => $this->faker()->password(6, 6),
            'password_confirmation' => '',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors('password');
    }

    /** @test */
    public function it_requires_a_role_to_create_a_user()
    {
        $response = $this->postJson(route('auth.signup'));

        $response->assertUnprocessable()
            ->assertJsonValidationErrors('role');
    }

    /** @test */
    public function it_requires_a_valid_role_to_create_a_user()
    {
        $response = $this->postJson(route('auth.signup'), ['role' => -1]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors('role');
    }

    /** @test */
    public function it_requires_a_document_to_create_a_user()
    {
        $response = $this->postJson(route('auth.signup'));

        $response->assertUnprocessable()
            ->assertJsonValidationErrors('document');
    }

    /** @test */
    public function it_requires_a_valid_document_to_create_a_user_for_a_person()
    {
        $response = $this->postJson(route('auth.signup'), [
            'document' => 'anything',
            'role' => Enums\Role::Person,
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors('document');
    }

    /** @test */
    public function it_requires_a_valid_document_to_create_a_user_for_a_store()
    {
        $response = $this->postJson(route('auth.signup'), [
            'document' => 'anything',
            'role' => Enums\Role::Store,
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors('document');
    }

    /** @test */
    public function it_requires_a_valid_person_document_to_create_a_user_for_a_person()
    {
        $response = $this->postJson(route('auth.signup'), [
            'document' => $this->faker('pt_BR')->cnpj(),
            'role' => Enums\Role::Person,
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors('document');
    }

    /** @test */
    public function it_requires_a_valid_store_document_to_create_a_user_for_a_store()
    {
        $response = $this->postJson(route('auth.signup'), [
            'document' => $this->faker('pt_BR')->cpf(),
            'role' => Enums\Role::Store,
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors('document');
    }

    /** @test */
    public function it_requires_a_not_registered_document_to_create_a_user()
    {
        $document = $this->faker('pt_BR')->cpf();

        User::factory()->create(['document' => $document]);

        $response = $this->postJson(route('auth.signup'), [
            'document' => $document,
            'role' => Enums\Role::Person,
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors('document');
    }

    /** @test */
    public function it_creates_a_user_for_a_person()
    {
        $creationPayload = User::factory()->raw(['role' => Enums\Role::Person]);

        $this->postJson(route('auth.signup'), $creationPayload);

        $this->assertDatabaseHas('users', [
            'first_name' => $creationPayload['first_name'],
            'last_name' => $creationPayload['last_name'],
            'email' => $creationPayload['email'],
            'document' => $creationPayload['document'],
        ]);
    }

    /** @test */
    public function it_returns_the_created_user()
    {
        $creationPayload = User::factory()->raw(['role' => Enums\Role::Person]);

        $response = $this->postJson(route('auth.signup'), $creationPayload);

        $response->assertCreated()
            ->assertJson(fn (AssertableJson $json) =>
                $json->has('id')
                    ->where('first_name', $creationPayload['first_name'])
                    ->where('last_name', $creationPayload['last_name'])
                    ->where('email', $creationPayload['email'])
                    ->where('document', $creationPayload['document'])
                    ->where('role.slug', $creationPayload['role']->value)
                    ->has('created_at')
            );
    }

    /** @test */
    public function it_sends_an_email_confirmation_message()
    {
        Notification::fake();

        $creationPayload = User::factory()->raw(['role' => Enums\Role::Person]);

        $this->postJson(route('auth.signup'), $creationPayload);

        Notification::assertSentTo(
            User::where('email', $creationPayload['email'])->first(),
            VerifyEmail::class,
        );
    }
}
