<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Order approval tracking
            $table->unsignedBigInteger('approver_id')->nullable()->after('seller_id');
            $table->timestamp('approved_at')->nullable()->after('status');
            $table->timestamp('rejected_at')->nullable()->after('approved_at');
            $table->text('rejection_reason')->nullable()->after('rejected_at');
            
            // Order fulfillment tracking
            $table->timestamp('fulfillment_started_at')->nullable()->after('rejection_reason');
            $table->timestamp('shipped_at')->nullable()->after('fulfillment_started_at');
            $table->timestamp('completed_at')->nullable()->after('shipped_at');
            $table->string('tracking_number')->nullable()->after('completed_at');
            $table->string('shipping_carrier')->nullable()->after('tracking_number');
            $table->timestamp('estimated_delivery')->nullable()->after('shipping_carrier');
            
            // Warehouse assignment
            $table->unsignedBigInteger('assigned_warehouse_id')->nullable()->after('estimated_delivery');
            
            // Order metadata
            $table->json('fulfillment_data')->nullable()->after('assigned_warehouse_id');
            $table->text('notes')->nullable()->after('fulfillment_data');
            $table->text('delivery_address')->nullable()->after('notes');
            $table->timestamp('expected_delivery_date')->nullable()->after('delivery_address');
            
            // Backorder tracking
            $table->unsignedBigInteger('parent_order_id')->nullable()->after('expected_delivery_date');
            $table->boolean('is_backorder')->default(false)->after('parent_order_id');
            
            // Error tracking
            $table->text('fulfillment_error')->nullable()->after('is_backorder');
            $table->timestamp('fulfillment_failed_at')->nullable()->after('fulfillment_error');
            
            // Foreign key constraints
            $table->foreign('approver_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('assigned_warehouse_id')->references('id')->on('warehouses')->onDelete('set null');
            $table->foreign('parent_order_id')->references('id')->on('orders')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['approver_id']);
            $table->dropForeign(['assigned_warehouse_id']);
            $table->dropForeign(['parent_order_id']);
            
            $table->dropColumn([
                'approver_id',
                'approved_at',
                'rejected_at',
                'rejection_reason',
                'fulfillment_started_at',
                'shipped_at',
                'completed_at',
                'tracking_number',
                'shipping_carrier',
                'estimated_delivery',
                'assigned_warehouse_id',
                'fulfillment_data',
                'notes',
                'delivery_address',
                'expected_delivery_date',
                'parent_order_id',
                'is_backorder',
                'fulfillment_error',
                'fulfillment_failed_at'
            ]);
        });
    }
};
