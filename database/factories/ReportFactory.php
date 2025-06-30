<?php

namespace Database\Factories;

use App\Models\Report;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Report>
 */
class ReportFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $types = ['sales', 'inventory', 'production', 'vendor_performance', 'retailer_performance'];
        $type = fake()->randomElement($types);

        return [
            'title' => ucfirst($type) . ' Report - ' . fake()->date('Y-m-d'),
            'type' => $type,
            'description' => fake()->paragraph(),
            'data' => $this->generateReportData($type),
            'generated_by' => User::factory(),
            'generated_at' => fake()->dateTimeBetween('-1 month', 'now'),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * Generate data for different report types.
     */
    private function generateReportData(string $type): array
    {
        switch ($type) {
            case 'sales':
                return [
                    'total_sales' => fake()->randomFloat(2, 10000, 1000000),
                    'units_sold' => fake()->numberBetween(100, 10000),
                    'period' => fake()->date('Y-m'),
                    'top_products' => $this->generateTopProducts(),
                    'sales_by_region' => $this->generateSalesByRegion(),
                ];

            case 'inventory':
                return [
                    'total_stock_value' => fake()->randomFloat(2, 50000, 5000000),
                    'low_stock_items' => fake()->numberBetween(0, 20),
                    'overstock_items' => fake()->numberBetween(0, 15),
                    'inventory_turnover' => fake()->randomFloat(2, 1, 10),
                    'warehouse_utilization' => fake()->randomFloat(2, 50, 95),
                ];

            case 'production':
                return [
                    'completed_units' => fake()->numberBetween(500, 5000),
                    'defect_rate' => fake()->randomFloat(2, 0.1, 5),
                    'efficiency' => fake()->randomFloat(2, 70, 98),
                    'production_delays' => fake()->numberBetween(0, 10),
                    'resource_utilization' => fake()->randomFloat(2, 60, 95),
                ];

            case 'vendor_performance':
                return [
                    'on_time_delivery' => fake()->randomFloat(2, 70, 100),
                    'quality_rating' => fake()->randomFloat(2, 3, 5),
                    'cost_efficiency' => fake()->randomFloat(2, 60, 95),
                    'communication_score' => fake()->randomFloat(2, 3, 5),
                    'vendors_reviewed' => fake()->numberBetween(5, 30),
                ];

            case 'retailer_performance':
                return [
                    'sales_performance' => fake()->randomFloat(2, 70, 120),
                    'customer_satisfaction' => fake()->randomFloat(2, 3, 5),
                    'market_share' => fake()->randomFloat(2, 1, 30),
                    'growth_rate' => fake()->randomFloat(2, -5, 20),
                    'retailers_reviewed' => fake()->numberBetween(5, 50),
                ];

            default:
                return [
                    'notes' => fake()->paragraphs(3, true),
                ];
        }
    }

    /**
     * Generate sample top products data.
     */
    private function generateTopProducts(): array
    {
        $products = [];

        for ($i = 0; $i < 5; $i++) {
            $products[] = [
                'name' => fake()->randomElement(['Aktina Pro', 'Aktina Lite', 'Aktina Max', 'Aktina Mini', 'Aktina Ultra']) . ' ' . fake()->numberBetween(10, 15),
                'units_sold' => fake()->numberBetween(100, 2000),
                'revenue' => fake()->randomFloat(2, 10000, 500000),
            ];
        }

        return $products;
    }

    /**
     * Generate sample sales by region data.
     */
    private function generateSalesByRegion(): array
    {
        $regions = ['North America', 'Europe', 'Asia', 'South America', 'Africa', 'Australia'];
        $data = [];

        foreach ($regions as $region) {
            $data[$region] = fake()->randomFloat(2, 10000, 300000);
        }

        return $data;
    }

    /**
     * Create a report of a specific type.
     */
    public function ofType(string $type): static
    {
        return $this->state(function (array $attributes) use ($type) {
            return [
                'type' => $type,
                'title' => ucfirst($type) . ' Report - ' . fake()->date('Y-m-d'),
                'data' => $this->generateReportData($type),
            ];
        });
    }

    /**
     * Create a report for a specific user.
     */
    public function generatedBy(User $user): static
    {
        return $this->state(function (array $attributes) use ($user) {
            return [
                'generated_by' => $user->id,
            ];
        });
    }
}
