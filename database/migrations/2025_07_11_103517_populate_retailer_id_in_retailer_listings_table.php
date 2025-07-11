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
        // Populate retailer_id from retailer_email by matching with users table
        DB::statement('
            UPDATE retailer_listings rl
            JOIN users u ON rl.retailer_email = u.email AND u.role = "retailer"
            SET rl.retailer_id = u.id
            WHERE rl.retailer_id IS NULL
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Clear retailer_id field (reversible)
        DB::table('retailer_listings')->update(['retailer_id' => null]);
    }
};
