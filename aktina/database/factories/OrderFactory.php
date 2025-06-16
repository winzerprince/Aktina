<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $requiredBy = fake()->dateTimeBetween('now', '+3 months');
        $deliveryDate = fake()->boolean(70) ?
            fake()->dateTimeBetween($requiredBy->format('Y-m-d'), $requiredBy->format('Y-m-d') . ' +1 month') :
            null;

        return [
            'order_number' => 'AKT-' . fake()->year() . '-' . str_pad(fake()->unique()->numberBetween(1, 9999), 4, '0', STR_PAD_LEFT),
            'supplier_id' => User::factory()->supplier(),
            'production_manager_id' => User::factory()->productionManager(),
            'title' => fake()->randomElement([
                'Raw materials for production line A',
                'Component supplies for line B',
                'Packaging materials',
                'Steel components',
                'Plastic pellets',
                'Electronics components',
                'Chemical additives',
                'Quality control equipment',
                'Safety equipment supplies',
                'Maintenance parts'
            ]),
            'description' => fake()->paragraph(),
            'status' => fake()->randomElement(['pending', 'confirmed', 'in_production', 'shipped', 'delivered']),
            'priority' => fake()->randomElement(['low', 'medium', 'high', 'urgent']),
            'total_amount' => fake()->randomFloat(2, 1000, 50000),
            'quantity' => fake()->numberBetween(1, 1000),
            'unit' => fake()->randomElement(['pcs', 'kg', 'tons', 'liters', 'boxes', 'pallets']),
            'required_by' => $requiredBy,
            'delivery_date' => $deliveryDate,
            'notes' => fake()->boolean(30) ? fake()->sentence() : null,
        ];
    }
}
