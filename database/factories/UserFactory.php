<?php

namespace Database\Factories;

use App\Enums;
use App\Models;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'document' => fake('pt_BR')->cpf(),
            'email' => fake()->safeEmail(),
            'role_id' => Models\Role::whereSlug(Enums\Role::Person)->value('id'),
            'email_verified_at' => now(),
            'password' => bcrypt('password'),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return static
     */
    public function unverified()
    {
        return $this->state(function (array $attributes) {
            return [
                'email_verified_at' => null,
            ];
        });
    }
}
