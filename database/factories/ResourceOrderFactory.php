<?php

namespace Database\Factories;

use App\Models\ResourceOrder;
use App\Models\User;
use App\Models\Resource;
use Illuminate\Database\Eloquent\Factories\Factory;

class ResourceOrderFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ResourceOrder::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Get a supplier user
        $supplier = User::where('role', 'supplier')->inRandomOrder()->first();

        // Get an admin user as Aktina representative
        $aktina = User::where('role', 'admin')->inRandomOrder()->first();

        // Get some random resources
        $resources = Resource::inRandomOrder()->limit(rand(1, 5))->get();

        $items = [];
        $price = 0;

        foreach ($resources as $resource) {
            $quantity = rand(10, 100);
            $items[] = [
                'resource_id' => $resource->id,
                'quantity' => $quantity,
                'unit_cost' => $resource->unit_cost,
                'name' => $resource->name,
            ];

            $price += $quantity * $resource->unit_cost;
        }

        return [
            'price' => $price,
            'items' => $items,
            'status' => $this->faker->randomElement([
                ResourceOrder::STATUS_PENDING,
                ResourceOrder::STATUS_ACCEPTED,
                ResourceOrder::STATUS_COMPLETE
            ]),
            'buyer_id' => $aktina->id, // Aktina is always the buyer for resource orders
            'seller_id' => $supplier->id,
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'updated_at' => function (array $attributes) {
                return $this->faker->dateTimeBetween($attributes['created_at'], 'now');
            },
        ];
    }

    /**
     * Set the order status to pending.
     */
    public function pending(): Factory
    {
        return $this->state(function () {
            return [
                'status' => ResourceOrder::STATUS_PENDING,
            ];
        });
    }

    /**
     * Set the order status to accepted.
     */
    public function accepted(): Factory
    {
        return $this->state(function () {
            return [
                'status' => ResourceOrder::STATUS_ACCEPTED,
            ];
        });
    }

    /**
     * Set the order status to complete.
     */
    public function complete(): Factory
    {
        return $this->state(function () {
            return [
                'status' => ResourceOrder::STATUS_COMPLETE,
            ];
        });
    }
}
