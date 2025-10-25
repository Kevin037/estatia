<?php

namespace Database\Seeders;

use App\Models\Type;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            ['name' => 'Type 36', 'land_area' => 72.00, 'building_area' => 36.00],
            ['name' => 'Type 45', 'land_area' => 90.00, 'building_area' => 45.00],
            ['name' => 'Type 54', 'land_area' => 120.00, 'building_area' => 54.00],
            ['name' => 'Type 60', 'land_area' => 150.00, 'building_area' => 60.00],
            ['name' => 'Type 70', 'land_area' => 175.00, 'building_area' => 70.00],
            ['name' => 'Type 90', 'land_area' => 200.00, 'building_area' => 90.00],
            ['name' => 'Type 120', 'land_area' => 250.00, 'building_area' => 120.00],
            ['name' => 'Type 150', 'land_area' => 300.00, 'building_area' => 150.00],
        ];

        foreach ($types as $type) {
            Type::create($type);
        }
    }
}
