<?php

namespace Database\Factories;

use App\Models\InventoryAlert;
use App\Models\Product;
use App\Models\Resource;
use Illuminate\Database\Eloquent\Factories\Factory;

class InventoryAlertFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = InventoryAlert::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $isProduct = $this->faker->boolean();
        
        return [
            'type' => $isProduct ? 'product' : 'resource',
            'product_id' => $isProduct ? function () {
                return Product::factory()->create()->id;
            } : null,
            'resource_id' => !$isProduct ? function () {
                return Resource::factory()->create()->id;
            } : null,
            'warehouse_id' => function () {
                return \App\Models\Warehouse::factory()->create()->id;
            },
            'current_level' => $this->faker->numberBetween(1, 5),
            'threshold_level' => $this->faker->numberBetween(5, 10),
            'severity' => $this->faker->randomElement(['critical', 'warning']),
            'resolved' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
    
    /**
     * Define a state for a critical alert.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function critical()
    {
        return $this->state(function (array $attributes) {
            return [
                'severity' => 'critical',
                'current_level' => $this->faker->numberBetween(0, 2),
            ];
        });
    }
    
    /**
     * Define a state for a warning alert.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function warning()
    {
        return $this->state(function (array $attributes) {
            return [
                'severity' => 'warning',
                'current_level' => $this->faker->numberBetween(3, 5),
            ];
        });
    }
    
    /**
     * Define a state for a resolved alert.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function resolved()
    {
        return $this->state(function (array $attributes) {
            return [
                'resolved' => true,
                'resolved_at' => now()->subHours($this->faker->numberBetween(1, 48)),
                'resolved_by' => function () {
                    return \App\Models\User::factory()->create()->id;
                },
            ];
        });
    }
}
