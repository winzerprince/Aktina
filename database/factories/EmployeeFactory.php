<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Employee>
 */
class EmployeeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $roles = [
            'Logistics Coordinator',
            'Warehouse Manager',
            'Inventory Specialist',
            'Shipping Coordinator',
            'Quality Control Specialist',
            'Supply Chain Analyst',
            'Order Fulfillment Specialist',
            'Production Line Worker',
            'Assembly Technician',
            'Packaging Specialist'
        ];

        return [
            'name' => $this->faker->name(),
            'role' => $this->faker->randomElement($roles),
            'status' => $this->faker->randomElement(['available', 'unavailable']),
            'current_activity' => $this->faker->randomElement(['none', 'managing_order', 'managing_production']),
            'order_id' => null,
            'production_id' => null,
        ];
    }
}
