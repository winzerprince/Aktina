<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Application;
use App\Models\RetailerListing;
use App\Models\Vendor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Vendor>
 */
class VendorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->vendor(),
            'retailer_listing_id' => null,
            'application_id' => null,
        ];
    }

    /**
     * Indicate that the vendor has an application.
     */
    public function withApplication(): static
    {
        return $this->state(fn (array $attributes) => [
            'application_id' => Application::factory(),
        ]);
    }

    /**
     * Indicate that the vendor has a retailer listing.
     */
    public function withRetailerListing(): static
    {
        return $this->state(fn (array $attributes) => [
            'retailer_listing_id' => RetailerListing::factory(),
        ]);
    }
}
