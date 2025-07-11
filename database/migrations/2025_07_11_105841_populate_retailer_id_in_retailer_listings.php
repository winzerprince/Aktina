<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Populate retailer_id from retailer_email where it's missing
        DB::statement("
            UPDATE retailer_listings
            SET retailer_id = (
                SELECT id FROM users
                WHERE users.email = retailer_listings.retailer_email
                AND users.role = 'retailer'
                LIMIT 1
            )
            WHERE retailer_id IS NULL
            AND retailer_email IS NOT NULL
        ");

        // For listings without retailer_email, assign to random retailers
        $listingsWithoutRetailer = DB::table('retailer_listings')
            ->whereNull('retailer_id')
            ->whereNull('retailer_email')
            ->get();

        $retailers = DB::table('users')
            ->where('role', 'retailer')
            ->pluck('id')
            ->toArray();

        if (!empty($retailers)) {
            foreach ($listingsWithoutRetailer as $listing) {
                $randomRetailerId = $retailers[array_rand($retailers)];
                $retailer = DB::table('users')->where('id', $randomRetailerId)->first();

                DB::table('retailer_listings')
                    ->where('id', $listing->id)
                    ->update([
                        'retailer_id' => $randomRetailerId,
                        'retailer_email' => $retailer->email
                    ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove retailer_id values that were populated by this migration
        // (Keep the test user connection)
        DB::table('retailer_listings')
            ->where('retailer_email', '!=', 'retailer@gmail.com')
            ->update(['retailer_id' => null]);
    }
};
