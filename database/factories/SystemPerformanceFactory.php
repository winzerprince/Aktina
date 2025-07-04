<?php

namespace Database\Factories;

use App\Models\SystemPerformance;
use Illuminate\Database\Eloquent\Factories\Factory;

class SystemPerformanceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = SystemPerformance::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'cpu_usage' => $this->faker->numberBetween(20, 100),
            'memory_usage' => $this->faker->numberBetween(30, 100),
            'disk_usage' => $this->faker->numberBetween(40, 100),
            'response_time' => $this->faker->numberBetween(50, 5000),
            'alert_messages' => $this->faker->randomElement([
                null, 
                json_encode(['CPU usage exceeded threshold', 'Disk usage critical'])
            ]),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
    
    /**
     * Define a state for a healthy system.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function healthy()
    {
        return $this->state(function (array $attributes) {
            return [
                'cpu_usage' => $this->faker->numberBetween(20, 60),
                'memory_usage' => $this->faker->numberBetween(30, 70),
                'disk_usage' => $this->faker->numberBetween(40, 80),
                'response_time' => $this->faker->numberBetween(50, 1000),
                'alert_messages' => null,
            ];
        });
    }
    
    /**
     * Define a state for a system with issues.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function withIssues()
    {
        return $this->state(function (array $attributes) {
            return [
                'cpu_usage' => $this->faker->numberBetween(81, 100),
                'memory_usage' => $this->faker->numberBetween(86, 100),
                'disk_usage' => $this->faker->numberBetween(91, 100),
                'response_time' => $this->faker->numberBetween(2001, 5000),
                'alert_messages' => json_encode([
                    'CPU usage critical: ' . $attributes['cpu_usage'] . '%',
                    'Memory usage critical: ' . $attributes['memory_usage'] . '%',
                    'Disk usage critical: ' . $attributes['disk_usage'] . '%',
                    'Response time critical: ' . $attributes['response_time'] . 'ms'
                ]),
            ];
        });
    }
}
