<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ResourceSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('resource')->insert([
            // Core Processing Components
            [
                'name' => 'Snapdragon 8 Gen 3',
                'component_type' => 'SoC',
                'category' => 'Core Processing Components',
                'units' => 1000,
                'reorder_level' => 200,
                'overstock_level' => 5000,
                'unit_cost' => 150.00,
                'part_number' => 'SM8650-AB',
                'specifications' => json_encode([
                    'process' => '4nm',
                    'cpu_cores' => '1x3.3GHz + 3x3.2GHz + 2x3.0GHz + 2x2.3GHz',
                    'gpu' => 'Adreno 750',
                    'ai_engine' => 'Hexagon NPU'
                ]),
                'description' => 'Flagship mobile processor for premium phones',
                'supplier_id' => 1,
                'bom_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'MediaTek Dimensity 9300',
                'component_type' => 'SoC',
                'category' => 'Core Processing Components',
                'units' => 800,
                'reorder_level' => 150,
                'overstock_level' => 4000,
                'unit_cost' => 120.00,
                'part_number' => 'MT6989',
                'specifications' => json_encode([
                    'process' => '4nm',
                    'cpu_cores' => '4x3.25GHz + 4x2.0GHz',
                    'gpu' => 'Mali-G720 MC12',
                    'ai_engine' => 'APU 790'
                ]),
                'description' => 'High-performance processor for flagship devices',
                'supplier_id' => 2,
                'bom_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Display Components
            [
                'name' => '6.7" AMOLED Display Panel',
                'component_type' => 'Display',
                'category' => 'Display & Visual Systems',
                'units' => 500,
                'reorder_level' => 100,
                'overstock_level' => 2000,
                'unit_cost' => 80.00,
                'part_number' => 'SAM-6.7-LTPO',
                'specifications' => json_encode([
                    'size' => '6.7 inches',
                    'resolution' => '2778 x 1284',
                    'refresh_rate' => '120Hz LTPO',
                    'brightness' => '1200 nits peak'
                ]),
                'description' => 'Premium AMOLED display with adaptive refresh rate',
                'supplier_id' => 3,
                'bom_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Camera Components
            [
                'name' => 'Sony IMX890 Main Camera',
                'component_type' => 'Camera Sensor',
                'category' => 'Camera Systems',
                'units' => 600,
                'reorder_level' => 120,
                'overstock_level' => 2500,
                'unit_cost' => 45.00,
                'part_number' => 'IMX890-AAJH5',
                'specifications' => json_encode([
                    'resolution' => '50MP',
                    'sensor_size' => '1/1.56"',
                    'pixel_size' => '1.0μm',
                    'features' => ['OIS', '8K video', 'HDR']
                ]),
                'description' => 'High-resolution main camera sensor with OIS',
                'supplier_id' => 4,
                'bom_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Battery Components
            [
                'name' => '5000mAh Li-ion Battery',
                'component_type' => 'Battery',
                'category' => 'Power Management',
                'units' => 800,
                'reorder_level' => 150,
                'overstock_level' => 3000,
                'unit_cost' => 25.00,
                'part_number' => 'CATL-5000-Li',
                'specifications' => json_encode([
                    'capacity' => '5000mAh',
                    'voltage' => '3.87V',
                    'chemistry' => 'Li-ion',
                    'fast_charging' => '67W'
                ]),
                'description' => 'High-capacity battery with fast charging support',
                'supplier_id' => 5,
                'bom_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Memory Components
            [
                'name' => '12GB LPDDR5 RAM',
                'component_type' => 'Memory',
                'category' => 'Core Processing Components',
                'units' => 1200,
                'reorder_level' => 250,
                'overstock_level' => 5000,
                'unit_cost' => 35.00,
                'part_number' => 'SK-LPDDR5-12GB',
                'specifications' => json_encode([
                    'capacity' => '12GB',
                    'type' => 'LPDDR5',
                    'speed' => '6400Mbps',
                    'power' => 'Low power consumption'
                ]),
                'description' => 'High-speed mobile RAM for flagship devices',
                'supplier_id' => 6,
                'bom_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
