<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ResourceOrder;
use App\Models\User;

class ResourceOrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create resource orders for each supplier
        $suppliers = User::where('role', 'supplier')->get();
        $aktina = User::where('role', 'admin')->first();

        if (!$aktina) {
            $this->command->error('No admin user found for Aktina company!');
            return;
        }

        foreach ($suppliers as $supplier) {
            // Create 3-7 resource orders for each supplier
            $numOrders = rand(3, 7);

            for ($i = 0; $i < $numOrders; $i++) {
                // Mix of different statuses
                $status = match (rand(0, 10)) {
                    0, 1, 2, 3 => ResourceOrder::STATUS_PENDING,
                    4, 5, 6, 7 => ResourceOrder::STATUS_ACCEPTED,
                    default => ResourceOrder::STATUS_COMPLETE,
                };

                ResourceOrder::factory()
                    ->state([
                        'status' => $status,
                        'buyer_id' => $aktina->id,
                        'seller_id' => $supplier->id,
                    ])
                    ->create();
            }
        }
    }
}
