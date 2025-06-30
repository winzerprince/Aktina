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
        Schema::table('retailers', function (Blueprint $table) {
            $table->string('business_name')->nullable()->after('user_id');
            $table->string('business_type')->nullable()->after('business_name');
            $table->string('business_registration_number')->nullable()->after('business_type');
            $table->string('tax_id')->nullable()->after('business_registration_number');
            $table->string('contact_person')->nullable()->after('tax_id');
            $table->string('phone')->nullable()->after('contact_person');
            $table->string('website')->nullable()->after('phone');
            $table->string('annual_revenue')->nullable()->after('website');
            $table->string('employee_count')->nullable()->after('annual_revenue');
            $table->integer('years_in_business')->nullable()->after('employee_count');
            $table->text('primary_products')->nullable()->after('years_in_business');
            $table->text('target_market')->nullable()->after('primary_products');
            $table->json('business_address')->nullable()->after('target_market');
            $table->boolean('demographics_completed')->default(false)->after('business_address');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('retailers', function (Blueprint $table) {
            $table->dropColumn([
                'business_name',
                'business_type',
                'business_registration_number',
                'tax_id',
                'contact_person',
                'phone',
                'website',
                'annual_revenue',
                'employee_count',
                'years_in_business',
                'primary_products',
                'target_market',
                'business_address',
                'demographics_completed'
            ]);
        });
    }
};
