<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $roles = ['admin', 'supplier', 'vendor', 'retailer', 'production_manager', 'hr_manager'];

        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'verified' => fake()->boolean(70), // 70% chance of being verified
            'is_verified' => fake()->boolean(70), // 70% chance of being verified
            'company_name' => fake()->optional(0.8)->company(), // 80% chance of having a company name
            'address' => json_encode([
                'street' => fake()->streetAddress(),
                'city' => fake()->city(),
                'state' => fake()->stateAbbr(),
                'zip' => fake()->postcode(),
                'country' => fake()->countryCode()
            ]),
            'remember_token' => Str::random(10),
            'role' => fake()->randomElement($roles),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Create a user with a specific role.
     */
    public function withRole(string $role): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => $role,
        ]);
    }

    /**
     * Create an admin user.
     */
    public function admin(): static
    {
        return $this->withRole('admin');
    }

    /**
     * Create a supplier user.
     */
    public function supplier(): static
    {
        return $this->withRole('supplier');
    }

    /**
     * Create a vendor user.
     */
    public function vendor(): static
    {
        return $this->withRole('vendor');
    }

    /**
     * Create a retailer user.
     */
    public function retailer(): static
    {
        return $this->withRole('retailer');
    }

    /**
     * Create a production manager user.
     */
    public function productionManager(): static
    {
        return $this->withRole('production_manager');
    }

    /**
     * Create an HR manager user.
     */
    public function hrManager(): static
    {
        return $this->withRole('hr_manager');
    }

    /**
     * Create a verified user.
     */
    public function verified(): static
    {
        return $this->state(fn (array $attributes) => [
            'verified' => true,
        ]);
    }

    /**
     * Create a user with a specific company name.
     */
    public function withCompany(string $companyName): static
    {
        return $this->state(fn (array $attributes) => [
            'company_name' => $companyName,
        ]);
    }
}
