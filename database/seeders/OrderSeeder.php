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

        // Create orders with existing users
        if ($users->count() >= 2) {
            for ($i = 0; $i < 20; $i++) {
                $buyer = $users->random();
                $seller = $users->where('id', '!=', $buyer->id)->random();

                Order::factory()->create([
                    'buyer_id' => $buyer->id,
                    'seller_id' => $seller->id,
                ]);
            }
        }

        // Create additional orders with new users
        Order::factory(30)->create();

        // Create some large orders
        Order::factory(8)->large()->create();
    }
}
