<?php

namespace Database\Factories;

use App\Models\Resource;
use App\Models\Supplier;
use App\Models\Bom;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Resource>
 */
class ResourceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */    public function definition(): array
    {
        $componentTypes = ['SoC', 'Display', 'Camera', 'Battery', 'Memory', 'Storage', 'Sensor'];
        $categories = ['Core Processing Components', 'Display Systems', 'Camera Systems', 'Power Management', 'Memory Systems'];

        return [
            'name' => $this->faker->company() . ' ' . $this->faker->randomElement($componentTypes),
            'component_type' => $this->faker->randomElement($componentTypes),
            'category' => $this->faker->randomElement($categories),
            'units' => $this->faker->numberBetween(0, 1000),
            'reorder_level' => $this->faker->numberBetween(50, 200),
            'overstock_level' => $this->faker->numberBetween(1000, 5000),
            'unit_cost' => $this->faker->randomFloat(2, 1.00, 200.00),
            'part_number' => strtoupper($this->faker->lexify('??')) . '-' . $this->faker->numerify('####'),
            'specifications' => json_encode([
                'material' => $this->faker->word(),
                'color' => $this->faker->colorName(),
                'weight' => $this->faker->numberBetween(1, 100) . 'g',
            ]),
            'description' => $this->faker->sentence(),
        ];
    }

    /**
     * Indicate that the resource is expensive.
     */
    public function expensive(): static
    {
        return $this->state(fn (array $attributes) => [
            'unit_cost' => $this->faker->randomFloat(2, 150.00, 500.00),
            'component_type' => 'SoC',
        ]);
    }

    /**
     * Indicate that the resource has low stock.
     */
    public function lowStock(): static
    {
        return $this->state(fn (array $attributes) => [
            'units' => $this->faker->numberBetween(0, 50),
        ]);
    }
}
