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
        Schema::table('resources', function (Blueprint $table) {
            $table->foreignId('warehouse_id')->nullable()->after('id')->constrained('warehouses')->onDelete('set null');
            $table->integer('reserved_quantity')->default(0)->after('units'); // Reserved for orders
            $table->integer('available_quantity')->default(0)->after('reserved_quantity'); // Available = units - reserved
            $table->timestamp('last_movement_at')->nullable()->after('overstock_level');
            $table->decimal('average_cost', 10, 2)->nullable()->after('unit_cost'); // Average cost calculation
            $table->string('location_code')->nullable()->after('part_number'); // Specific location within warehouse
            
            $table->index(['warehouse_id', 'units']);
            $table->index('available_quantity');
            $table->index('last_movement_at');
        });
    }

    public function down(): void
    {
        Schema::table('resources', function (Blueprint $table) {
            $table->dropForeign(['warehouse_id']);
            $table->dropIndex(['warehouse_id', 'units']);
            $table->dropIndex(['available_quantity']);
            $table->dropIndex(['last_movement_at']);
            $table->dropColumn([
                'warehouse_id', 
                'reserved_quantity', 
                'available_quantity', 
                'last_movement_at',
                'average_cost',
                'location_code'
            ]);
        });
    }
};
