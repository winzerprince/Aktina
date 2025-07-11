<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // Create exactly 6 Aktina products with logical company-based quantities
        $products = [
            [
                'name' => 'Aktina Pro 15',
                'model' => 'AKT-PRO-15',
                'sku' => 'AKT-PRO-15-256',
                'description' => 'Flagship smartphone with cutting-edge technology',
                'msrp' => 999.99,
                'category' => 'smartphone',
                'target_market' => 'flagship',
                'specifications' => [
                    'display' => '6.7" AMOLED, 120Hz',
                    'processor' => 'Snapdragon 8 Gen 3',
                    'ram' => '12GB LPDDR5',
                    'storage' => '256GB UFS 4.0',
                    'camera' => '50MP main + 12MP ultra-wide',
                    'battery' => '5000mAh with 67W charging',
                ],
                'company_quantities' => [
                    'Aktina' => ['quantity' => 1000, 'updated_at' => now()->toISOString()],
                    'Vendor Company' => ['quantity' => 300, 'updated_at' => now()->toISOString()],
                    'Retailer Company' => ['quantity' => 50, 'updated_at' => now()->toISOString()],
                ]
            ],
            [
                'name' => 'Aktina Lite 12',
                'model' => 'AKT-LITE-12',
                'sku' => 'AKT-LITE-12-128',
                'description' => 'Mid-range smartphone with excellent value',
                'msrp' => 449.99,
                'category' => 'smartphone',
                'target_market' => 'mid-range',
                'specifications' => [
                    'display' => '6.4" AMOLED, 90Hz',
                    'processor' => 'MediaTek Dimensity 8200',
                    'ram' => '8GB LPDDR5',
                    'storage' => '128GB UFS 3.1',
                    'camera' => '48MP main + 8MP ultra-wide',
                    'battery' => '4500mAh with 33W charging',
                ],
                'company_quantities' => [
                    'Aktina' => ['quantity' => 1500, 'updated_at' => now()->toISOString()],
                    'Vendor Company' => ['quantity' => 400, 'updated_at' => now()->toISOString()],
                    'Retailer Company' => ['quantity' => 75, 'updated_at' => now()->toISOString()],
                ]
            ],
            [
                'name' => 'Aktina Essential 10',
                'model' => 'AKT-ESS-10',
                'sku' => 'AKT-ESS-10-64',
                'description' => 'Budget-friendly smartphone with reliable performance',
                'msrp' => 199.99,
                'category' => 'smartphone',
                'target_market' => 'budget',
                'specifications' => [
                    'display' => '6.1" IPS LCD, 60Hz',
                    'processor' => 'MediaTek Helio G85',
                    'ram' => '4GB LPDDR4X',
                    'storage' => '64GB eMMC',
                    'camera' => '13MP main + 2MP depth',
                    'battery' => '3000mAh with 18W charging',
                ],
                'company_quantities' => [
                    'Aktina' => ['quantity' => 2000, 'updated_at' => now()->toISOString()],
                    'Vendor Company' => ['quantity' => 600, 'updated_at' => now()->toISOString()],
                    'Retailer Company' => ['quantity' => 100, 'updated_at' => now()->toISOString()],
                ]
            ],
            [
                'name' => 'Aktina Tab 10',
                'model' => 'AKT-TAB-10',
                'sku' => 'AKT-TAB-10-128',
                'description' => 'Premium tablet for productivity and entertainment',
                'msrp' => 599.99,
                'category' => 'tablet',
                'target_market' => 'mid-range',
                'specifications' => [
                    'display' => '10.5" IPS LCD, 2K',
                    'processor' => 'MediaTek Dimensity 9000',
                    'ram' => '8GB LPDDR5',
                    'storage' => '128GB UFS 3.1',
                    'camera' => '13MP rear + 8MP front',
                    'battery' => '8000mAh with 45W charging',
                ],
                'company_quantities' => [
                    'Aktina' => ['quantity' => 800, 'updated_at' => now()->toISOString()],
                    'Vendor Company' => ['quantity' => 200, 'updated_at' => now()->toISOString()],
                    'Retailer Company' => ['quantity' => 30, 'updated_at' => now()->toISOString()],
                ]
            ],
            [
                'name' => 'Aktina Watch 3',
                'model' => 'AKT-WATCH-3',
                'sku' => 'AKT-WATCH-3-GPS',
                'description' => 'Advanced smartwatch with health monitoring',
                'msrp' => 299.99,
                'category' => 'smartwatch',
                'target_market' => 'mid-range',
                'specifications' => [
                    'display' => '1.4" AMOLED, Always-on',
                    'processor' => 'Qualcomm W5 Gen 1',
                    'storage' => '32GB',
                    'connectivity' => 'GPS, WiFi, Bluetooth 5.3',
                    'sensors' => 'Heart rate, SpO2, GPS',
                    'battery' => '400mAh, 2-day life',
                ],
                'company_quantities' => [
                    'Aktina' => ['quantity' => 600, 'updated_at' => now()->toISOString()],
                    'Vendor Company' => ['quantity' => 150, 'updated_at' => now()->toISOString()],
                    'Retailer Company' => ['quantity' => 25, 'updated_at' => now()->toISOString()],
                ]
            ],
            [
                'name' => 'Aktina Buds Pro',
                'model' => 'AKT-BUDS-PRO',
                'sku' => 'AKT-BUDS-PRO-ANC',
                'description' => 'Premium wireless earbuds with active noise cancellation',
                'msrp' => 179.99,
                'category' => 'earbuds',
                'target_market' => 'mid-range',
                'specifications' => [
                    'drivers' => '11mm dynamic drivers',
                    'anc' => 'Active Noise Cancellation',
                    'battery' => '6h + 24h case',
                    'connectivity' => 'Bluetooth 5.3, USB-C',
                    'features' => 'Touch controls, IPX5',
                    'codec' => 'SBC, AAC, LDAC',
                ],
                'company_quantities' => [
                    'Aktina' => ['quantity' => 1200, 'updated_at' => now()->toISOString()],
                    'Vendor Company' => ['quantity' => 350, 'updated_at' => now()->toISOString()],
                    'Retailer Company' => ['quantity' => 60, 'updated_at' => now()->toISOString()],
                ]
            ]
        ];

        foreach ($products as $productData) {
            Product::create($productData);
        }
    }
}
