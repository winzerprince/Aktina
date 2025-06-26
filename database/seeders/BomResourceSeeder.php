<?php

namespace Database\Seeders;

use App\Models\Bom;
use App\Models\Resource;
use Illuminate\Database\Seeder;

class BomResourceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all BOMs and Resources
        $boms = Bom::all();
        $resources = Resource::all();

        if ($boms->count() > 0 && $resources->count() > 0) {
            // Attach random resources to each BOM
            foreach ($boms as $bom) {
                // Each BOM should have 3-8 resources
                $resourceCount = rand(3, min(8, $resources->count()));
                $randomResources = $resources->random($resourceCount);

                $bom->resources()->attach($randomResources);
            }
        }
    }
}
