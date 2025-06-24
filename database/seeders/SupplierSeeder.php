<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('supplier')->insert([
            [
                'user_id' => 2,
                'company_name' => 'Qualcomm Technologies',
                'region' => 'US',
                'component_categories' => json_encode(['SoC', 'Modem', 'Audio', 'Connectivity']),
                'reliability_rating' => 4.8,
                'is_preferred' => true,
                'certifications' => 'ISO 9001, Conflict-free minerals, RoHS compliant',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => null,
                'company_name' => 'MediaTek Inc.',
                'region' => 'Asia-Pacific',
                'component_categories' => json_encode(['SoC', 'Connectivity', 'AI Processing']),
                'reliability_rating' => 4.6,
                'is_preferred' => true,
                'certifications' => 'ISO 14001, IATF 16949',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => null,
                'company_name' => 'Samsung Display',
                'region' => 'Asia-Pacific',
                'component_categories' => json_encode(['Display', 'Touch Controller', 'OLED']),
                'reliability_rating' => 4.9,
                'is_preferred' => true,
                'certifications' => 'ISO 9001, Environmental certifications',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => null,
                'company_name' => 'Sony Semiconductor',
                'region' => 'Asia-Pacific',
                'component_categories' => json_encode(['Camera Sensor', 'Image Processing']),
                'reliability_rating' => 4.9,
                'is_preferred' => true,
                'certifications' => 'ISO 14001, Conflict-free sourcing',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => null,
                'company_name' => 'CATL Battery',
                'region' => 'Asia-Pacific',
                'component_categories' => json_encode(['Battery', 'Power Management']),
                'reliability_rating' => 4.7,
                'is_preferred' => true,
                'certifications' => 'ISO 9001, UL certification, UN38.3',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => null,
                'company_name' => 'SK Hynix',
                'region' => 'Asia-Pacific',
                'component_categories' => json_encode(['Memory', 'Storage', 'DRAM']),
                'reliability_rating' => 4.8,
                'is_preferred' => true,
                'certifications' => 'ISO 9001, JEDEC standards',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
