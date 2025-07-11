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
        // Get actual products or create sample items
        $products = \App\Models\Product::all();

        $numItems = $this->faker->numberBetween(1, 5);
        $items = [];
        $totalPrice = 0;

        for ($i = 0; $i < $numItems; $i++) {
            $quantity = $this->faker->numberBetween(1, 10);
            $unitPrice = $this->faker->randomFloat(2, 10, 500);

            // Use actual product ID if products exist, otherwise use placeholder
            $productId = $products->isNotEmpty()
                ? $products->random()->id
                : $this->faker->numberBetween(1, 6); // Fallback to 1-6 range for our 6 products

            $items[] = [
                'product_id' => $productId,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'total' => $quantity * $unitPrice,
            ];
            $totalPrice += $quantity * $unitPrice;
        }

        return [
            'price' => $totalPrice,
            'items' => json_encode($items),
            'status' => $this->faker->randomElement(['pending', 'accepted', 'complete']),
            'buyer_id' => User::factory(), // User acting as buyer
            'seller_id' => User::factory(), // User acting as seller
        ];
    }    /**
     * Indicate that the order is large (high value).
     */
    public function large(): static
    {
        return $this->state(function (array $attributes) {
            $products = \App\Models\Product::all();
            $items = [];
            $totalPrice = 0;
            $numItems = $this->faker->numberBetween(5, 15);

            for ($i = 0; $i < $numItems; $i++) {
                $quantity = $this->faker->numberBetween(5, 50);
                $unitPrice = $this->faker->randomFloat(2, 100, 1000);

                // Use actual product ID if products exist, otherwise use placeholder
                $productId = $products->isNotEmpty()
                    ? $products->random()->id
                    : $this->faker->numberBetween(1, 6); // Fallback to 1-6 range for our 6 products

                $items[] = [
                    'product_id' => $productId,
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

    /**
     * Set the order status to pending.
     */
    public function pending(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'pending',
            ];
        });
    }

    /**
     * Set the order status to accepted.
     */
    public function accepted(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'accepted',
            ];
        });
    }

    /**
     * Set the order status to complete.
     */
    public function complete(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'complete',
            ];
        });
    }
}
