<?php

namespace Database\Factories;

use App\Models\RetailerListing;
use App\Models\Application;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RetailerListing>
 */
class RetailerListingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */    public function definition(): array
    {
        // Get a random retailer user to connect to
        $retailerUser = \App\Models\User::where('role', 'retailer')->inRandomOrder()->first();

        return [
            'retailer_email' => $retailerUser ? $retailerUser->email : $this->faker->unique()->safeEmail(),
            'retailer_id' => $retailerUser?->id,
            'application_id' => Application::factory(),
        ];
    }

    /**
     * Indicate that the listing is for a specific application.
     */
    public function forApplication($applicationId): static
    {
        return $this->state(fn (array $attributes) => [
            'application_id' => $applicationId,
        ]);
    }

    /**
     * Indicate that the listing is for a specific retailer.
     */
    public function forRetailer($retailerUser): static
    {
        return $this->state(fn (array $attributes) => [
            'retailer_email' => $retailerUser->email,
            'retailer_id' => $retailerUser->id,
        ]);
    }
}
