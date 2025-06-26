<?php

namespace Database\Factories;

use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Supplier>
 */
class SupplierFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $regions = ['Asia-Pacific', 'Europe', 'North America', 'South America', 'Africa', 'Middle East'];
        $categories = ['Processors', 'Memory', 'Storage', 'Displays', 'Cameras', 'Sensors', 'Batteries', 'Connectivity'];
        $certifications = ['ISO 9001', 'ISO 14001', 'Conflict-free minerals', 'RoHS compliance', 'REACH compliance'];

        return [
            'user_id' => User::factory()->supplier(),
            'company_name' => $this->faker->company() . ' ' . $this->faker->randomElement(['Electronics', 'Components', 'Tech', 'Systems', 'Solutions']),
            'region' => $this->faker->randomElement($regions),
            'component_categories' => json_encode($this->faker->randomElements($categories, $this->faker->numberBetween(1, 4))),
            'reliability_rating' => $this->faker->randomFloat(2, 3.0, 5.0),
            'is_preferred' => $this->faker->boolean(30), // 30% chance of being preferred
            'certifications' => implode(', ', $this->faker->randomElements($certifications, $this->faker->numberBetween(1, 3))),
            'resources' => json_encode([]),
        ];
    }

    /**
     * Indicate that the supplier is a preferred supplier.
     */
    public function preferred(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_preferred' => true,
            'reliability_rating' => $this->faker->randomFloat(2, 4.5, 5.0),
        ]);
    }

    /**
     * Create a supplier for a specific user.
     */
    public function forUser(int $userId): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $userId,
        ]);
    }
}
