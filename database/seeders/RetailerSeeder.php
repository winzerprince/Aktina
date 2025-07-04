<?php

namespace Database\Seeders;

use App\Models\Retailer;
use App\Models\User;
use Illuminate\Database\Seeder;

class RetailerSeeder extends Seeder
{
    public function run(): void
    {
        // Get users with retailer role
        $retailerUsers = User::where('role', 'retailer')->get();

        // Create retailers linked to existing users with explicit demographic data
        foreach ($retailerUsers as $user) {
            // Special setup for test retailer user
            if ($user->email === 'retailer@gmail.com') {
                Retailer::factory()->forUser($user->id)->create([
                    'city' => 'Test City',
                    'urban_rural_classification' => 'urban',
                    'customer_age_class' => 'adult',
                    'customer_income_bracket' => 'high',
                    'customer_education_level' => 'high',
                    'male_female_ratio' => 1.5,
                ]);
            } else {
                Retailer::factory()->forUser($user->id)->create([
                    // Each existing user gets specific demographic data
                    'city' => fake()->city(),
                    'urban_rural_classification' => fake()->randomElement(['urban', 'suburban', 'rural']),
                    'customer_age_class' => fake()->randomElement(['teenager', 'youth', 'adult']),
                    'customer_income_bracket' => fake()->randomElement(['medium', 'high']),
                    'customer_education_level' => fake()->randomElement(['mid', 'high']),
                    'male_female_ratio' => fake()->randomFloat(2, 1.2, 2.2),
                ]);
            }
        }

        // Create retailers for different market segments

        // Urban youth retailers (3)
        Retailer::factory(3)->create([
            'urban_rural_classification' => 'urban',
            'customer_age_class' => 'youth',
            'customer_income_bracket' => 'medium',
            'male_female_ratio' => fake()->randomFloat(2, 1.0, 1.2),
        ]);

        // Premium urban retailers (3)
        Retailer::factory(3)->create([
            'urban_rural_classification' => 'urban',
            'customer_age_class' => 'adult',
            'customer_income_bracket' => 'high',
            'customer_education_level' => 'high',
        ]);

        // Suburban family retailers (3)
        Retailer::factory(3)->create([
            'urban_rural_classification' => 'suburban',
            'customer_age_class' => 'adult',
            'customer_income_bracket' => 'medium',
            'male_female_ratio' => fake()->randomFloat(2, 0.8, 1.2),
        ]);

        // Rural retailers (3)
        Retailer::factory(3)->create([
            'urban_rural_classification' => 'rural',
            'customer_age_class' => 'adult',
            'customer_income_bracket' => 'low',
            'customer_education_level' => 'mid',
        ]);

        // Educational retailers (targeting schools/universities) (3)
        Retailer::factory(3)->create([
            'customer_age_class' => 'teenager',
            'customer_education_level' => 'high',
            'male_female_ratio' => 1.0,
        ]);

        // Senior-focused retailers (3)
        Retailer::factory(3)->create([
            'customer_age_class' => 'senior',
            'customer_income_bracket' => 'medium',
            'urban_rural_classification' => 'suburban',
        ]);

        // Create some verified retailers with random demographics
        Retailer::factory(5)->verified()->create();

        // Create some large business retailers with higher income demographics
        Retailer::factory(5)->largeBusiness()->create([
            'customer_income_bracket' => 'high',
            'customer_education_level' => 'high',
        ]);
    }
}
