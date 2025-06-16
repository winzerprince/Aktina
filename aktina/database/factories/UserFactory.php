<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
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
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'role' => fake()->randomElement(array_keys(User::ROLES)),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Create a supplier user.
     */
    public function supplier(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'supplier',
        ]);
    }

    /**
     * Create a production manager user.
     */
    public function productionManager(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'production_manager',
        ]);
    }

    /**
     * Create an HR manager user.
     */
    public function hrManager(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'hr_manager',
        ]);
    }

    /**
     * Create a system administrator user.
     */
    public function systemAdministrator(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'system_administrator',
        ]);
    }

    /**
     * Create a wholesaler user.
     */
    public function wholesaler(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'wholesaler',
        ]);
    }

    /**
     * Create a retailer user.
     */
    public function retailer(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'retailer',
        ]);
    }
}
