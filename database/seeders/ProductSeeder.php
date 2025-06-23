<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('product')->insert([
            [
                'name' => 'Aktina Pro 15',
                'model' => 'AKT-PRO-15',
                'sku' => 'AKT-PRO-15-256',
                'description' => 'Flagship smartphone with cutting-edge technology and premium build quality',
                'msrp' => 999.99,
                'category' => 'smartphone',
                'specifications' => json_encode([
                    'display' => '6.7" AMOLED, 120Hz LTPO',
                    'processor' => 'Snapdragon 8 Gen 3',
                    'ram' => '12GB LPDDR5',
                    'storage' => '256GB UFS 4.0',
                    'camera' => '50MP main + 12MP ultra-wide + 10MP telephoto',
                    'battery' => '5000mAh with 67W fast charging',
                    'os' => 'Android 14'
                ]),
                'target_market' => 'flagship',
                'bom_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Aktina Lite 12',
                'model' => 'AKT-LITE-12',
                'sku' => 'AKT-LITE-12-128',
                'description' => 'Mid-range smartphone offering excellent value with essential features',
                'msrp' => 449.99,
                'category' => 'smartphone',
                'specifications' => json_encode([
                    'display' => '6.4" AMOLED, 90Hz',
                    'processor' => 'MediaTek Dimensity 9300',
                    'ram' => '8GB LPDDR5',
                    'storage' => '128GB UFS 3.1',
                    'camera' => '48MP main + 8MP ultra-wide',
                    'battery' => '4500mAh with 33W fast charging',
                    'os' => 'Android 14'
                ]),
                'target_market' => 'mid-range',
                'bom_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Aktina Essential 10',
                'model' => 'AKT-ESS-10',
                'sku' => 'AKT-ESS-10-64',
                'description' => 'Budget-friendly smartphone with reliable performance and essential features',
                'msrp' => 199.99,
                'category' => 'smartphone',
                'specifications' => json_encode([
                    'display' => '6.1" IPS LCD, 60Hz',
                    'processor' => 'MediaTek Helio G85',
                    'ram' => '4GB LPDDR4X',
                    'storage' => '64GB eMMC',
                    'camera' => '13MP main + 2MP depth',
                    'battery' => '3000mAh with 18W charging',
                    'os' => 'Android 14 Go Edition'
                ]),
                'target_market' => 'budget',
                'bom_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
