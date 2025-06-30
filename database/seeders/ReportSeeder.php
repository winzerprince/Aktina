<?php

namespace Database\Seeders;

use App\Models\Report;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReportSeeder extends Seeder
{
    public function run(): void
    {
        // Find admin users to be report generators
        $admins = User::where('role', 'admin')->get();

        if ($admins->isEmpty()) {
            // Create a default admin if none exists
            $admin = User::factory()->admin()->create();
            $admins = collect([$admin]);
        }

        // Create various types of reports
        $reportTypes = ['sales', 'inventory', 'production', 'vendor_performance', 'retailer_performance'];

        foreach ($reportTypes as $type) {
            // Create multiple reports of each type with different admin users as generators
            foreach ($admins->take(2) as $admin) {
                Report::factory()
                    ->ofType($type)
                    ->generatedBy($admin)
                    ->count(3)
                    ->create();
            }
        }

        // Create some additional random reports
        Report::factory()->count(10)->create();
    }
}
