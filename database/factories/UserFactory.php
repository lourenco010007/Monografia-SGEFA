<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nome' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'password' => static::$password ??= Hash::make('password'),
            'perfil' => User::PROFILE_TENANT,
            'activo' => true,
        ];
    }

    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'perfil' => User::PROFILE_ADMIN,
        ]);
    }

    public function agent(): static
    {
        return $this->state(fn (array $attributes) => [
            'perfil' => User::PROFILE_AGENT,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'activo' => false,
        ]);
    }

}
