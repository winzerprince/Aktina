<?php

namespace Database\Seeders;

use App\Models\Resource;
use App\Models\Supplier;
use App\Models\Bom;
use Illuminate\Database\Seeder;

class ResourceSeeder extends Seeder
{
    public function run(): void
    {        // Get existing suppliers and BOMs to link resources
        $suppliers = Supplier::all();
        $boms = Bom::all();

        // Create resources for existing BOMs and suppliers
        if ($suppliers->count() > 0 && $boms->count() > 0) {
            foreach ($boms->take(15) as $bom) {
                // Create 3-8 resources per BOM
                $resourceCount = rand(3, 8);
                for ($i = 0; $i < $resourceCount; $i++) {
                    Resource::factory()->create();
                }
            }
        }

        // Create additional resources
        Resource::factory(50)->create();

        // Create some expensive resources
        Resource::factory(10)->expensive()->create();

        // Create some resources with low stock
        Resource::factory(8)->lowStock()->create();
    }
}
