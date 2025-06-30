<?php

namespace Database\Factories;

use App\Models\Application;
use App\Models\Vendor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Application>
 */
class ApplicationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'status' => $this->faker->randomElement(['pending', 'partially approved', 'approved', 'rejected']),
            'meeting_schedule' => $this->faker->optional(0.7)->dateTimeBetween('now', '+1 month'), // 70% chance of having a meeting
            'vendor_id' => null, // Will be set by the seeder to avoid circular dependency
            'pdf_path' => $this->faker->optional(0.8)->randomElement([
                'storage/applications/app_' . $this->faker->uuid . '.pdf',
                'storage/applications/vendor_' . $this->faker->uuid . '.pdf',
                'storage/applications/application_' . $this->faker->numerify('######') . '.pdf',
            ]),
            'form_data' => $this->generateFormData(),
            'processed_by_java_server' => $this->faker->boolean(60), // 60% chance of being processed
            'processing_date' => $this->faker->optional(0.6)->dateTimeBetween('-1 month', 'now'),
            'processing_notes' => $this->faker->optional(0.5)->paragraph(),
            'application_reference' => 'APP-' . $this->faker->unique()->numerify('######'),
        ];
    }

    /**
     * Generate sample form data for PDF applications.
     */
    private function generateFormData(): array
    {
        return [
            'company_info' => [
                'name' => $this->faker->company(),
                'registration_number' => $this->faker->numerify('REG-########'),
                'tax_id' => $this->faker->numerify('TAX-########'),
                'founding_year' => $this->faker->numberBetween(1980, 2022),
                'employees' => $this->faker->numberBetween(10, 5000),
            ],
            'contact_info' => [
                'address' => $this->faker->address(),
                'phone' => $this->faker->phoneNumber(),
                'email' => $this->faker->companyEmail(),
                'website' => $this->faker->url(),
                'contact_person' => $this->faker->name(),
            ],
            'business_details' => [
                'industry' => $this->faker->randomElement(['Electronics', 'Retail', 'Manufacturing', 'Technology', 'Services']),
                'annual_revenue' => $this->faker->randomElement(['< $1M', '$1M - $10M', '$10M - $50M', '$50M - $100M', '> $100M']),
                'markets_served' => $this->faker->randomElements(['North America', 'Europe', 'Asia', 'South America', 'Africa', 'Australia'], $this->faker->numberBetween(1, 3)),
                'years_in_business' => $this->faker->numberBetween(1, 50),
            ],
            'questionnaire' => [
                'why_partner' => $this->faker->paragraph(),
                'distribution_capacity' => $this->faker->numberBetween(100, 10000) . ' units/month',
                'existing_partnerships' => $this->faker->randomElements(['Samsung', 'Apple', 'Sony', 'LG', 'Huawei', 'Xiaomi'], $this->faker->numberBetween(0, 3)),
                'certifications' => $this->faker->randomElements(['ISO 9001', 'ISO 14001', 'Green Business', 'Fair Trade'], $this->faker->numberBetween(0, 2)),
            ],
            'application_info' => [
                'submission_date' => $this->faker->date(),
                'preferred_contact_method' => $this->faker->randomElement(['Email', 'Phone', 'Mail']),
                'referral_source' => $this->faker->randomElement(['Website', 'Trade Show', 'Referral', 'Advertisement', 'Social Media']),
            ],
        ];
    }

    /**
     * Indicate that the application is approved.
     */
    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'approved',
            'meeting_schedule' => $this->faker->dateTimeBetween('-1 month', 'now'),
        ]);
    }

    /**
     * Indicate that the application is pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
            'meeting_schedule' => $this->faker->dateTimeBetween('now', '+2 weeks'),
        ]);
    }
}
