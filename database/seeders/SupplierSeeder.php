<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('supplier')->insert([
            [
                'user_id' => 2, // John Supplier
                'resources' => json_encode(['steel', 'aluminum', 'plastic']),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
