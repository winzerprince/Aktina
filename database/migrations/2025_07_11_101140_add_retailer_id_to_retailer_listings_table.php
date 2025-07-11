<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('retailer_listings', function (Blueprint $table) {
            $table->foreignId('retailer_id')->nullable()->after('retailer_email')->constrained('users')->onDelete('cascade');
            $table->index(['application_id', 'retailer_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('retailer_listings', function (Blueprint $table) {
            $table->dropForeign(['retailer_id']);
            $table->dropIndex(['application_id', 'retailer_id']);
            $table->dropColumn('retailer_id');
        });
    }
};
