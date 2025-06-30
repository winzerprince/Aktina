<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        // Get existing users to act as buyers and sellers
        $users = User::all();

        // Create orders with existing users - distribute among different statuses
        if ($users->count() >= 2) {
            // Create pending orders
            for ($i = 0; $i < 10; $i++) {
                $buyer = $users->random();
                $seller = $users->where('id', '!=', $buyer->id)->random();

                Order::factory()->pending()->create([
                    'buyer_id' => $buyer->id,
                    'seller_id' => $seller->id,
                ]);
            }

            // Create accepted orders
            for ($i = 0; $i < 5; $i++) {
                $buyer = $users->random();
                $seller = $users->where('id', '!=', $buyer->id)->random();

                Order::factory()->accepted()->create([
                    'buyer_id' => $buyer->id,
                    'seller_id' => $seller->id,
                ]);
            }

            // Create completed orders
            for ($i = 0; $i < 5; $i++) {
                $buyer = $users->random();
                $seller = $users->where('id', '!=', $buyer->id)->random();

                Order::factory()->complete()->create([
                    'buyer_id' => $buyer->id,
                    'seller_id' => $seller->id,
                ]);
            }
        }

        // Create additional orders with new users with random status
        Order::factory(20)->create();

        // Create some large orders with specific statuses
        Order::factory(3)->large()->pending()->create();
        Order::factory(2)->large()->accepted()->create();
        Order::factory(3)->large()->complete()->create();
    }
}
