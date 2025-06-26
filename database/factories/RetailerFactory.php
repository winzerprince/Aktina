<?php

namespace Database\Factories;

use App\Models\Retailer;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Retailer>
 */
class RetailerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */    public function definition(): array
    {
        return [
            'user_id' => User::factory()->retailer(),
        ];
    }

    /**
     * Indicate that the retailer is linked to an existing user.
     */
    public function forUser($userId): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $userId,
        ]);
    }

    /**
     * Create a verified retailer with a verified user.
     */
    public function verified(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => User::factory()->retailer()->verified(),
        ]);
    }

    /**
     * Create a large business retailer.
     */
    public function largeBusiness(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => User::factory()->retailer()->withCompany($this->faker->company() . ' Corporation'),
        ]);
    }
}
