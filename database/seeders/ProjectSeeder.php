<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Land;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if lands exist, if not create a sample land
        if (Land::count() === 0) {
            $land = Land::create([
                'name' => 'Main Construction Site',
                'address' => 'Jl. Raya Pembangunan No. 123',
                'area' => 5000.00,
                'price' => 2500000000.00,
                'status' => 'available',
            ]);
        } else {
            $land = Land::first();
        }

        $projects = [
            [
                'name' => 'Green Valley Residence',
                'status' => 'in_progress',
                'land_id' => $land->id,
                'dt_start' => now()->subMonths(6),
                'dt_end' => now()->addMonths(12),
            ],
            [
                'name' => 'Sunset Park Housing',
                'status' => 'in_progress',
                'land_id' => $land->id,
                'dt_start' => now()->subMonths(3),
                'dt_end' => now()->addMonths(15),
            ],
            [
                'name' => 'Royal Estate Phase 1',
                'status' => 'pending',
                'land_id' => $land->id,
                'dt_start' => now()->addMonths(1),
                'dt_end' => now()->addMonths(18),
            ],
            [
                'name' => 'Modern Living Complex',
                'status' => 'in_progress',
                'land_id' => $land->id,
                'dt_start' => now()->subMonths(4),
                'dt_end' => now()->addMonths(10),
            ],
            [
                'name' => 'Harmony Hills Development',
                'status' => 'completed',
                'land_id' => $land->id,
                'dt_start' => now()->subYears(2),
                'dt_end' => now()->subMonths(3),
            ],
        ];

        foreach ($projects as $project) {
            Project::create($project);
        }

        $this->command->info('Projects seeded successfully.');
    }
}
