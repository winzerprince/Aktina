<?php

namespace Database\Factories;

use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Supplier>
 */
class SupplierFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Using 6 specific suppliers for different components
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

        $selectedSupplier = $this->faker->randomElement($suppliers);

        return [
            'user_id' => User::factory()->supplier(),
            'company_name' => $selectedSupplier['company_name'],
            'region' => $selectedSupplier['region'],
            'component_categories' => json_encode($selectedSupplier['component_categories']),
            'reliability_rating' => $selectedSupplier['reliability_rating'],
            'is_preferred' => $selectedSupplier['is_preferred'],
            'certifications' => $selectedSupplier['certifications'],
            'resources' => json_encode([]),
        ];
    }

    /**
     * Indicate that the supplier is a preferred supplier.
     */
    public function preferred(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_preferred' => true,
            'reliability_rating' => $this->faker->randomFloat(2, 4.5, 5.0),
        ]);
    }

    /**
     * Create a supplier for a specific user.
     */
    public function forUser(int $userId): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $userId,
        ]);
    }
}
