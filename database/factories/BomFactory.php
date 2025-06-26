<?php

namespace Database\Factories;

use App\Models\Bom;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Bom>
 */
class BomFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'price' => $this->faker->randomFloat(2, 50.00, 500.00),
            'product_id' => Product::factory(),
        ];
    }

    /**
     * Indicate that the BOM is for an expensive product.
     */
    public function expensive(): static
    {
        return $this->state(fn (array $attributes) => [
            'price' => $this->faker->randomFloat(2, 400.00, 800.00),
        ]);
    }

    /**
     * Indicate that the BOM is for a budget product.
     */
    public function budget(): static
    {
        return $this->state(fn (array $attributes) => [
            'price' => $this->faker->randomFloat(2, 20.00, 100.00),
        ]);
    }
}
