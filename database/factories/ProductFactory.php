<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */    public function definition(): array
    {
        $categories = ['smartphone', 'tablet', 'laptop', 'smartwatch', 'headphones'];
        $brands = ['Aktina Pro', 'Aktina Lite', 'Aktina Essential', 'Aktina Max', 'Aktina Ultra'];

        return [
            'name' => $this->faker->randomElement($brands) . ' ' . $this->faker->numberBetween(10, 20),
            'model' => 'AKT-' . strtoupper($this->faker->lexify('???')) . '-' . $this->faker->numberBetween(10, 99),
            'sku' => 'SKU-' . $this->faker->unique()->numerify('########'),
            'description' => $this->faker->paragraph(2),
            'msrp' => $this->faker->randomFloat(2, 99.99, 2999.99),
            'category' => $this->faker->randomElement($categories),
            'specifications' => json_encode([
                'color' => $this->faker->colorName(),
                'weight' => $this->faker->numberBetween(100, 500) . 'g',
                'warranty' => $this->faker->randomElement(['1 year', '2 years', '3 years']),
            ]),
            'target_market' => $this->faker->randomElement(['flagship', 'mid-range', 'budget']),
            'owner_id' => User::factory(),
        ];
    }

    /**
     * Indicate that the product is a premium item.
     */
    public function premium(): static
    {
        return $this->state(fn (array $attributes) => [
            'msrp' => $this->faker->randomFloat(2, 1500, 3000),
            'target_market' => 'flagship',
        ]);
    }

    /**
     * Indicate that the product is budget-friendly.
     */
    public function budget(): static
    {
        return $this->state(fn (array $attributes) => [
            'msrp' => $this->faker->randomFloat(2, 50, 300),
            'target_market' => 'budget',
        ]);
    }

    /**
     * Set the product's owner.
     */
    public function ownedBy($userId): static
    {
        return $this->state(function (array $attributes) use ($userId) {
            return [
                'owner_id' => $userId,
            ];
        });
    }
}
