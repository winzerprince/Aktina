<?php

namespace Database\Factories;

use App\Models\Application;
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
            'meeting_schedule' => $this->faker->dateTimeBetween('now', '+1 month'),
            'vendor_id' => Vendor::factory, // Assuming vendor_id is nullable and will be set later
        ];
    }
}
