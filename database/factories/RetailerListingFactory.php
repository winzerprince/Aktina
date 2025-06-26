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
        return [
            'retailer_email' => $this->faker->unique()->safeEmail(),
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
}
