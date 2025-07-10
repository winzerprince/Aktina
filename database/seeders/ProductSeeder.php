<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // Create unique products with company quantities
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
                    'Aktina' => ['quantity' => 500, 'updated_at' => now()->toISOString()],
                    'TechCorpDistribution' => ['quantity' => 200, 'updated_at' => now()->toISOString()],
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
                    'Aktina' => ['quantity' => 800, 'updated_at' => now()->toISOString()],
                    'RetailPlusChain' => ['quantity' => 300, 'updated_at' => now()->toISOString()],
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
                    'Aktina' => ['quantity' => 1000, 'updated_at' => now()->toISOString()],
                    'BudgetElectronics' => ['quantity' => 500, 'updated_at' => now()->toISOString()],
                ]
            ]
        ];

        foreach ($products as $productData) {
            Product::create($productData);
        }
    }
}
