<?php

namespace Database\Factories;

use App\Models\Application;
use App\Models\Vendor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Application>
 */
class ApplicationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'status' => $this->faker->randomElement(['pending', 'partially approved', 'approved', 'rejected']),
            'meeting_schedule' => $this->faker->optional(0.7)->dateTimeBetween('now', '+1 month'), // 70% chance of having a meeting
            'vendor_id' => null, // Will be set by the seeder to avoid circular dependency
        ];
    }

    /**
     * Indicate that the application is approved.
     */
    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'approved',
            'meeting_schedule' => $this->faker->dateTimeBetween('-1 month', 'now'),
        ]);
    }

    /**
     * Indicate that the application is pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
            'meeting_schedule' => $this->faker->dateTimeBetween('now', '+2 weeks'),
        ]);
    }
}
