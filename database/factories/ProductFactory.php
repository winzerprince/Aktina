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
        // Using only specific product models as required
        $products = [
            [
                'name' => 'Aktina 26 Pro',
                'model' => 'AKT-26-PRO',
                'category' => 'smartphone',
                'target_market' => 'flagship',
                'msrp' => 1299.99,
                'specifications' => [
                    'display' => '6.7-inch Super AMOLED',
                    'processor' => 'Aktina A16 Bionic',
                    'ram' => '12GB',
                    'storage' => '256GB',
                    'camera' => '108MP + 48MP + 12MP',
                    'battery' => '5000mAh',
                    'color' => $this->faker->randomElement(['Black', 'Silver', 'Gold']),
                    'weight' => '210g',
                    'warranty' => '2 years',
                ],
            ],
            [
                'name' => 'Sakina 26 Mini',
                'model' => 'SAK-26-MINI',
                'category' => 'smartphone',
                'target_market' => 'mid-range',
                'msrp' => 799.99,
                'specifications' => [
                    'display' => '5.4-inch AMOLED',
                    'processor' => 'Aktina A15 Chip',
                    'ram' => '8GB',
                    'storage' => '128GB',
                    'camera' => '64MP + 12MP',
                    'battery' => '4000mAh',
                    'color' => $this->faker->randomElement(['Blue', 'Red', 'Green']),
                    'weight' => '180g',
                    'warranty' => '1 year',
                ],
            ],
            [
                'name' => 'Akta Odyssey Tab',
                'model' => 'AKT-ODY-TAB',
                'category' => 'tablet',
                'target_market' => 'flagship',
                'msrp' => 999.99,
                'specifications' => [
                    'display' => '11-inch Retina XDR',
                    'processor' => 'Aktina M2 Chip',
                    'ram' => '16GB',
                    'storage' => '512GB',
                    'camera' => '12MP + 10MP',
                    'battery' => '10000mAh',
                    'color' => $this->faker->randomElement(['Space Gray', 'Silver']),
                    'weight' => '470g',
                    'warranty' => '2 years',
                ],
            ],
            [
                'name' => 'Aktina Buds Pro',
                'model' => 'AKT-BUDS-PRO',
                'category' => 'audio',
                'target_market' => 'flagship',
                'msrp' => 249.99,
                'specifications' => [
                    'type' => 'In-ear',
                    'connectivity' => 'Wireless',
                    'noise_cancellation' => 'Active',
                    'battery' => '30 hours with case',
                    'color' => $this->faker->randomElement(['Black', 'White', 'Navy']),
                    'weight' => '5g per bud',
                    'warranty' => '1 year',
                ],
            ],
            [
                'name' => 'Akta Sound',
                'model' => 'AKT-SOUND-SPK',
                'category' => 'audio',
                'target_market' => 'mid-range',
                'msrp' => 179.99,
                'specifications' => [
                    'type' => 'Bluetooth Speaker',
                    'connectivity' => 'Bluetooth 5.2',
                    'power' => '30W',
                    'battery' => '12 hours',
                    'color' => $this->faker->randomElement(['Black', 'White', 'Blue']),
                    'weight' => '950g',
                    'warranty' => '1 year',
                ],
            ],
            [
                'name' => 'Aktina 26',
                'model' => 'AKT-26-REG',
                'category' => 'smartphone',
                'target_market' => 'mid-range',
                'msrp' => 899.99,
                'specifications' => [
                    'display' => '6.1-inch OLED',
                    'processor' => 'Aktina A15 Chip',
                    'ram' => '6GB',
                    'storage' => '128GB',
                    'camera' => '50MP + 12MP',
                    'battery' => '4500mAh',
                    'color' => $this->faker->randomElement(['Black', 'White', 'Blue']),
                    'weight' => '195g',
                    'warranty' => '1 year',
                ],
            ],
            [
                'name' => 'Sakina 26',
                'model' => 'SAK-26-REG',
                'category' => 'smartphone',
                'target_market' => 'budget',
                'msrp' => 499.99,
                'specifications' => [
                    'display' => '6.1-inch LCD',
                    'processor' => 'Aktina A14 Chip',
                    'ram' => '4GB',
                    'storage' => '64GB',
                    'camera' => '48MP + 8MP',
                    'battery' => '4000mAh',
                    'color' => $this->faker->randomElement(['Black', 'White', 'Red']),
                    'weight' => '190g',
                    'warranty' => '1 year',
                ],
            ],
        ];

        $selectedProduct = $this->faker->randomElement($products);
        $sku = 'SKU-' . strtoupper(substr($selectedProduct['model'], 0, 3)) . '-' . $this->faker->unique()->numerify('####');

        return [
            'name' => $selectedProduct['name'],
            'model' => $selectedProduct['model'],
            'sku' => $sku,
            'description' => $this->faker->paragraph(2),
            'msrp' => $selectedProduct['msrp'],
            'category' => $selectedProduct['category'],
            'specifications' => $selectedProduct['specifications'],
            'target_market' => $selectedProduct['target_market'],
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
