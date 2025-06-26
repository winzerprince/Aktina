<?php

namespace Database\Seeders;

use App\Models\Resource;
use Illuminate\Database\Seeder;

class ResourceSeeder extends Seeder
{
    public function run(): void
    {
        // Create resources (relationships with BOMs will be handled via pivot table)
        Resource::factory(50)->create();

        // Create some expensive resources
        Resource::factory(10)->expensive()->create();

        // Create some resources with low stock
        Resource::factory(8)->lowStock()->create();
    }
}
