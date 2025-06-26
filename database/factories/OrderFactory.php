<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use App\Models\Product;
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
     */    public function definition(): array
    {
        // Create some sample products for the order
        $numItems = $this->faker->numberBetween(1, 5);
        $items = [];
        $totalPrice = 0;

        for ($i = 0; $i < $numItems; $i++) {
            $quantity = $this->faker->numberBetween(1, 10);
            $unitPrice = $this->faker->randomFloat(2, 10, 500);
            $items[] = [
                'product_id' => $this->faker->numberBetween(1, 50), // Assuming products exist
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'total' => $quantity * $unitPrice,
            ];
            $totalPrice += $quantity * $unitPrice;
        }

        return [
            'price' => $totalPrice,
            'items' => json_encode($items),
            'buyer_id' => User::factory(), // User acting as buyer
            'seller_id' => User::factory(), // User acting as seller
        ];
    }    /**
     * Indicate that the order is large (high value).
     */
    public function large(): static
    {
        return $this->state(function (array $attributes) {
            $items = [];
            $totalPrice = 0;
            $numItems = $this->faker->numberBetween(5, 15);

            for ($i = 0; $i < $numItems; $i++) {
                $quantity = $this->faker->numberBetween(5, 50);
                $unitPrice = $this->faker->randomFloat(2, 100, 1000);
                $items[] = [
                    'product_id' => $this->faker->numberBetween(1, 50),
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'total' => $quantity * $unitPrice,
                ];
                $totalPrice += $quantity * $unitPrice;
            }

            return [
                'price' => $totalPrice,
                'items' => json_encode($items),
            ];
        });
    }
}
