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
        // Add supplier_id and price fields to resource table
        Schema::table('resource', function (Blueprint $table) {
            // First add the column if it doesn't exist
            if (!Schema::hasColumn('resource', 'supplier_id')) {
                $table->unsignedBigInteger('supplier_id')->nullable();



            }
            if (!Schema::hasColumn('resource', 'bom_id')) {
                $table->unsignedBigInteger('bom_id')->nullable();


            }
            // Then add the foreign key constraint
            $table->foreign('supplier_id')
                ->references('id')
                ->on('supplier')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('bom_id')
                ->references('id')
                ->on('bom')
                ->onUpdate('cascade')
                ->onDelete('cascade');

        });

        Schema::table('product', function (Blueprint $table) {
            // First add the column if it doesn't exist
            if (!Schema::hasColumn('product', 'bom_id')) {
            $table->unsignedBigInteger('bom_id')->nullable();
            }

            // Then add the foreign key constraint
            $table->foreign('bom_id')
            ->references('id')
            ->on('bom')
            ->onUpdate('cascade')
            ->onDelete('cascade');
        });

        // Add product_id to production table
        Schema::table('production', function (Blueprint $table) {
            if (!Schema::hasColumn('production', 'product_id')) {
                $table->unsignedBigInteger('product_id')->nullable();
            }

            $table->foreign('product_id')
                ->references('id')
                ->on('product')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });

        // Add product_id to bom table
        Schema::table('bom', function (Blueprint $table) {
            if (!Schema::hasColumn('bom', 'product_id')) {
                $table->unsignedBigInteger('product_id')->nullable();
            }

            $table->foreign('product_id')
                ->references('id')
                ->on('product')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });

        // Add user_id to supplier table
        Schema::table('supplier', function (Blueprint $table) {
            if (!Schema::hasColumn('supplier', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable();
            }

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });

        // Add user_id to product_manager table
        Schema::table('product_manager', function (Blueprint $table) {
            if (!Schema::hasColumn('product_manager', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable();
            }

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });

        // Add user_id to admin table
        Schema::table('admin', function (Blueprint $table) {
            if (!Schema::hasColumn('admin', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable();
            }

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });

        // Add user_id to hr_manager table
        Schema::table('hr_manager', function (Blueprint $table) {
            if (!Schema::hasColumn('hr_manager', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable();
            }

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });

        // Add user_id to vendor table
        Schema::table('vendor', function (Blueprint $table) {
            if (!Schema::hasColumn('vendor', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable();
            }

            if (!Schema::hasColumn('vendor', 'retailer_listing_id')) {
                $table->unsignedBigInteger('retailer_listing_id')->nullable();
            }

            if (!Schema::hasColumn('vendor', 'application_id')) {
                $table->unsignedBigInteger('application_id')->nullable();
            }

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('retailer_listing_id')
                ->references('id')
                ->on('retailer_listing')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('application_id')
                ->references('id')
                ->on('application')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });

        // Add user_id to retailer table
        Schema::table('retailer', function (Blueprint $table) {
            if (!Schema::hasColumn('retailer', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable();
            }

            if (!Schema::hasColumn('retailer', 'vendor_id')) {
                $table->unsignedBigInteger('vendor_id')->nullable();
            }

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('vendor_id')
                ->references('id')
                ->on('vendor')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });

        // Add vendor_id to application table
        Schema::table('application', function (Blueprint $table) {
            if (!Schema::hasColumn('application', 'vendor_id')) {
                $table->unsignedBigInteger('vendor_id')->nullable();
            }

            $table->foreign('vendor_id')
                ->references('id')
                ->on('vendor')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });

        // Add product_id and retailer_id to rating table
        Schema::table('rating', function (Blueprint $table) {
            if (!Schema::hasColumn('rating', 'product_id')) {
                $table->unsignedBigInteger('product_id')->nullable();
            }

            if (!Schema::hasColumn('rating', 'retailer_id')) {
                $table->unsignedBigInteger('retailer_id')->nullable();
            }

            $table->foreign('product_id')
                ->references('id')
                ->on('product')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('retailer_id')
                ->references('id')
                ->on('retailer')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });

        // Add application_id to retailer_listing table
        Schema::table('retailer_listing', function (Blueprint $table) {
            if (!Schema::hasColumn('retailer_listing', 'application_id')) {
                $table->unsignedBigInteger('application_id')->nullable();
            }

            $table->foreign('application_id')
                ->references('id')
                ->on('application')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the foreign key constraints (not the columns)
        Schema::table('resource', function (Blueprint $table) {
            $table->dropForeign(['supplier_id']);
            $table->dropForeign(['bom_id']);
        });

        Schema::table('production', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
        });

        Schema::table('bom', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
        });

        Schema::table('supplier', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        Schema::table('product_manager', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        Schema::table('admin', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        Schema::table('hr_manager', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        Schema::table('vendor', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['retailer_listing_id']);
            $table->dropForeign(['application_id']);
        });

        Schema::table('retailer', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['vendor_id']);
        });

        Schema::table('application', function (Blueprint $table) {
            $table->dropForeign(['vendor_id']);
        });

        Schema::table('rating', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->dropForeign(['retailer_id']);
        });

        Schema::table('retailer_listing', function (Blueprint $table) {
            $table->dropForeign(['application_id']);
        });
    }
};
