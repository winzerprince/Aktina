<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\User;
use App\Models\Product;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        // Get test users for specific supply chain relationships
        $adminUser = User::where('email', 'admin@gmail.com')->first();
        $vendorUser = User::where('email', 'vendor@gmail.com')->first();
        $retailerUser = User::where('email', 'retailer@gmail.com')->first();
        $supplierUser = User::where('email', 'supplier@gmail.com')->first();

        // Get all users by company type for realistic order flows
        $aktinaUsers = User::where('company_name', 'Aktina')->get();
        $vendorUsers = User::where('company_name', 'Vendor Company')->get();
        $retailerUsers = User::where('company_name', 'Retailer Company')->get();
        $supplierUsers = User::where('company_name', 'Supplier Company')->get();

        // Get all products for order items
        $products = Product::all();

        // === SUPPLY CHAIN ORDERS ===

        // 1. Supplier → Aktina orders (raw materials/components)
        if ($supplierUsers->isNotEmpty() && $aktinaUsers->isNotEmpty()) {
            foreach ($supplierUsers->take(3) as $supplier) {
                $buyer = $aktinaUsers->random();
                $this->createSupplyChainOrder($supplier, $buyer, $products, 'raw_materials');
            }
        }

        // 2. Aktina → Vendor orders (finished products for distribution)
        if ($aktinaUsers->isNotEmpty() && $vendorUsers->isNotEmpty()) {
            foreach ($vendorUsers->take(5) as $vendor) {
                $seller = $aktinaUsers->random();
                $this->createSupplyChainOrder($seller, $vendor, $products, 'distribution');
            }
        }

        // 3. Vendor → Retailer orders (products for retail)
        if ($vendorUsers->isNotEmpty() && $retailerUsers->isNotEmpty()) {
            foreach ($retailerUsers->take(4) as $retailer) {
                $seller = $vendorUsers->random();
                $this->createSupplyChainOrder($seller, $retailer, $products, 'retail');
            }
        }

        // 4. Direct Aktina → Retailer orders (some direct sales)
        if ($aktinaUsers->isNotEmpty() && $retailerUsers->isNotEmpty()) {
            for ($i = 0; $i < 3; $i++) {
                $seller = $aktinaUsers->random();
                $buyer = $retailerUsers->random();
                $this->createSupplyChainOrder($seller, $buyer, $products, 'direct_retail');
            }
        }

        // === MAINTAIN TEST USER SPECIFIC ORDERS ===

        // Create specific orders for vendor@gmail.com as seller (maintains existing functionality)
        if ($vendorUser) {
            $buyers = User::where('id', '!=', $vendorUser->id)->limit(10)->get();

            if ($buyers->isNotEmpty()) {
                // Vendor as seller - pending orders
                for ($i = 0; $i < 5; $i++) {
                    $buyer = $buyers->random();
                    Order::factory()->pending()->create([
                        'buyer_id' => $buyer->id,
                        'seller_id' => $vendorUser->id,
                    ]);
                }

                // Vendor as seller - accepted orders
                for ($i = 0; $i < 3; $i++) {
                    $buyer = $buyers->random();
                    Order::factory()->accepted()->create([
                        'buyer_id' => $buyer->id,
                        'seller_id' => $vendorUser->id,
                    ]);
                }

                // Vendor as seller - complete orders
                for ($i = 0; $i < 2; $i++) {
                    $buyer = $buyers->random();
                    Order::factory()->complete()->create([
                        'buyer_id' => $buyer->id,
                        'seller_id' => $vendorUser->id,
                    ]);
                }
            }
        }

        // === ADDITIONAL RANDOM ORDERS ===

        // Create additional pending orders across all users
        $allUsers = User::all();
        if ($allUsers->count() >= 2) {
            for ($i = 0; $i < 8; $i++) {
                $buyer = $allUsers->random();
                $seller = $allUsers->where('id', '!=', $buyer->id)->random();

                Order::factory()->pending()->create([
                    'buyer_id' => $buyer->id,
                    'seller_id' => $seller->id,
                ]);
            }

            // Additional accepted orders
            for ($i = 0; $i < 5; $i++) {
                $buyer = $allUsers->random();
                $seller = $allUsers->where('id', '!=', $buyer->id)->random();

                Order::factory()->accepted()->create([
                    'buyer_id' => $buyer->id,
                    'seller_id' => $seller->id,
                ]);
            }

            // Additional completed orders
            for ($i = 0; $i < 5; $i++) {
                $buyer = $allUsers->random();
                $seller = $allUsers->where('id', '!=', $buyer->id)->random();

                Order::factory()->complete()->create([
                    'buyer_id' => $buyer->id,
                    'seller_id' => $seller->id,
                ]);
            }
        }

        // Create additional orders with factory defaults
        Order::factory(15)->create();

        // Create large orders with specific statuses
        Order::factory(3)->large()->pending()->create();
        Order::factory(2)->large()->accepted()->create();
        Order::factory(3)->large()->complete()->create();
    }

    /**
     * Create realistic supply chain orders based on relationship type
     */
    private function createSupplyChainOrder($seller, $buyer, $products, $type)
    {
        $items = [];
        $totalPrice = 0;

        // Determine order characteristics based on supply chain position
        switch ($type) {
            case 'raw_materials':
                // Suppliers provide raw materials - lower unit prices, higher quantities
                $numItems = rand(1, 3);
                $basePrice = rand(50, 200);
                $baseQuantity = rand(100, 500);
                break;

            case 'distribution':
                // Aktina to Vendor - medium prices, medium quantities
                $numItems = rand(2, 4);
                $basePrice = rand(200, 800);
                $baseQuantity = rand(50, 200);
                break;

            case 'retail':
                // Vendor to Retailer - higher prices, lower quantities
                $numItems = rand(1, 3);
                $basePrice = rand(400, 1200);
                $baseQuantity = rand(10, 50);
                break;

            case 'direct_retail':
                // Direct Aktina to Retailer - competitive prices, varied quantities
                $numItems = rand(1, 4);
                $basePrice = rand(300, 1000);
                $baseQuantity = rand(20, 100);
                break;

            default:
                $numItems = rand(1, 3);
                $basePrice = rand(100, 500);
                $baseQuantity = rand(10, 100);
        }

        // Create order items using actual products
        for ($i = 0; $i < $numItems; $i++) {
            $product = $products->random();
            $quantity = $baseQuantity + rand(-20, 20);
            $unitPrice = $basePrice + rand(-50, 100);

            $items[] = [
                'product_id' => $product->id,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'total' => $quantity * $unitPrice,
            ];
            $totalPrice += $quantity * $unitPrice;
        }

        // Create the order with appropriate status distribution
        $statuses = ['pending', 'accepted', 'complete'];
        $weights = [0.5, 0.3, 0.2]; // 50% pending, 30% accepted, 20% complete
        $status = $this->weightedRandom($statuses, $weights);

        Order::create([
            'buyer_id' => $buyer->id,
            'seller_id' => $seller->id,
            'price' => $totalPrice,
            'items' => json_encode($items),
            'status' => $status,
        ]);
    }

    /**
     * Select random element based on weights
     */
    private function weightedRandom($items, $weights)
    {
        $total = array_sum($weights);
        $random = mt_rand() / mt_getrandmax() * $total;

        $sum = 0;
        for ($i = 0; $i < count($items); $i++) {
            $sum += $weights[$i];
            if ($random <= $sum) {
                return $items[$i];
            }
        }

        return $items[0]; // fallback
    }
}
