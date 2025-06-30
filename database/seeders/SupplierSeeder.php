<?php

namespace Database\Seeders;

use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        // The 6 specific suppliers for different components
        $suppliers = [
            [
                'company_name' => 'GlassTech Industries',
                'region' => 'Asia-Pacific',
                'component_categories' => ['Displays', 'Glass Components', 'Screen Protectors'],
                'reliability_rating' => 4.8,
                'is_preferred' => true,
                'certifications' => 'ISO 9001, ISO 14001, RoHS compliance',
            ],
            [
                'company_name' => 'Silicon Valley Semiconductors',
                'region' => 'North America',
                'component_categories' => ['Processors', 'SoCs', 'Memory Chips'],
                'reliability_rating' => 4.9,
                'is_preferred' => true,
                'certifications' => 'ISO 9001, Conflict-free minerals, REACH compliance',
            ],
            [
                'company_name' => 'PowerCell Technologies',
                'region' => 'Europe',
                'component_categories' => ['Batteries', 'Power Management Units'],
                'reliability_rating' => 4.7,
                'is_preferred' => true,
                'certifications' => 'ISO 14001, RoHS compliance',
            ],
            [
                'company_name' => 'OpticalVision Systems',
                'region' => 'Asia-Pacific',
                'component_categories' => ['Cameras', 'Image Sensors', 'Lenses'],
                'reliability_rating' => 4.6,
                'is_preferred' => false,
                'certifications' => 'ISO 9001, ISO 14001',
            ],
            [
                'company_name' => 'ConnectX Solutions',
                'region' => 'Europe',
                'component_categories' => ['Connectivity', 'Antennas', 'Wireless Modules'],
                'reliability_rating' => 4.5,
                'is_preferred' => false,
                'certifications' => 'ISO 9001, RoHS compliance',
            ],
            [
                'company_name' => 'AudioWave Technologies',
                'region' => 'North America',
                'component_categories' => ['Audio Components', 'Speakers', 'Microphones'],
                'reliability_rating' => 4.4,
                'is_preferred' => false,
                'certifications' => 'ISO 14001, REACH compliance',
            ],
        ];

        // Create supplier users and attach the supplier data
        foreach ($suppliers as $supplierData) {
            $user = User::factory()->create([
                'role' => 'supplier',
                'company_name' => $supplierData['company_name'],
                'verified' => true,
            ]);

            Supplier::create([
                'user_id' => $user->id,
                'company_name' => $supplierData['company_name'],
                'region' => $supplierData['region'],
                'component_categories' => json_encode($supplierData['component_categories']),
                'reliability_rating' => $supplierData['reliability_rating'],
                'is_preferred' => $supplierData['is_preferred'],
                'certifications' => $supplierData['certifications'],
                'resources' => json_encode([]),
            ]);
        }
    }
}
