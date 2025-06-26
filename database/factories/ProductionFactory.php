<?php

namespace Database\Factories;

use App\Models\Production;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Production>
 */
class ProductionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */    public function definition(): array
    {
        return [
            'units' => $this->faker->numberBetween(100, 10000),
            'status' => $this->faker->randomElement(['pending', 'in_progress', 'completed', 'cancelled']),
            'completed_units' => function (array $attributes) {
                return $attributes['status'] === 'completed' ? $attributes['units'] : $this->faker->numberBetween(0, $attributes['units']);
            },
            'in_progress_units' => function (array $attributes) {
                return $attributes['status'] === 'in_progress' ? $this->faker->numberBetween(0, $attributes['units']) : 0;
            },
            'cancelled_units' => function (array $attributes) {
                return $attributes['status'] === 'cancelled' ? $attributes['units'] : 0;
            },
            'assembly_line' => $this->faker->randomElement(['Line 1', 'Line 2', 'Line 3', 'Line 4']),
            'product_id' => Product::factory(),
        ];
    }

    /**
     * Indicate that the production is completed.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
            'completed_units' => $attributes['units'],
            'in_progress_units' => 0,
            'cancelled_units' => 0,
        ]);
    }

    /**
     * Indicate that the production is in progress.
     */
    public function inProgress(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'in_progress',
            'completed_units' => $this->faker->numberBetween(0, (int)($attributes['units'] * 0.7)),
            'in_progress_units' => $this->faker->numberBetween(1, (int)($attributes['units'] * 0.3)),
            'cancelled_units' => 0,
        ]);
    }
}
