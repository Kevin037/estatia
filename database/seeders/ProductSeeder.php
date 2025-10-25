<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Formula;
use App\Models\Type;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get formula and type IDs
        $formulas = Formula::pluck('id')->toArray();
        $types = Type::pluck('id')->toArray();

        if (empty($formulas)) {
            $this->command->warn('No formulas found. Please seed formulas first.');
            return;
        }

        if (empty($types)) {
            $this->command->warn('No types found. Please seed types first.');
            return;
        }

        $products = [
            [
                'name' => 'Type 36 - Standard',
                'sku' => 'SKU-001',
                'code' => 'PROD-001',
                'type_id' => $types[array_rand($types)],
                'price' => 250000000,
                'qty' => 0,
                'formula_id' => $formulas[array_rand($formulas)],
            ],
            [
                'name' => 'Type 45 - Medium',
                'sku' => 'SKU-002',
                'code' => 'PROD-002',
                'type_id' => $types[array_rand($types)],
                'price' => 350000000,
                'qty' => 0,
                'formula_id' => $formulas[array_rand($formulas)],
            ],
            [
                'name' => 'Type 54 - Large',
                'sku' => 'SKU-003',
                'code' => 'PROD-003',
                'type_id' => $types[array_rand($types)],
                'price' => 450000000,
                'qty' => 0,
                'formula_id' => $formulas[array_rand($formulas)],
            ],
            [
                'name' => 'Type 60 - Extra Large',
                'sku' => 'SKU-004',
                'code' => 'PROD-004',
                'type_id' => $types[array_rand($types)],
                'price' => 550000000,
                'qty' => 0,
                'formula_id' => $formulas[array_rand($formulas)],
            ],
            [
                'name' => 'Type 40 - Compact',
                'sku' => 'SKU-005',
                'code' => 'PROD-005',
                'type_id' => $types[array_rand($types)],
                'price' => 320000000,
                'qty' => 0,
                'formula_id' => $formulas[array_rand($formulas)],
            ],
            [
                'name' => 'Type 38 - Economy',
                'sku' => 'SKU-006',
                'code' => 'PROD-006',
                'type_id' => $types[array_rand($types)],
                'price' => 280000000,
                'qty' => 0,
                'formula_id' => $formulas[array_rand($formulas)],
            ],
            [
                'name' => 'Type 50 - Premium',
                'sku' => 'SKU-007',
                'code' => 'PROD-007',
                'type_id' => $types[array_rand($types)],
                'price' => 380000000,
                'qty' => 0,
                'formula_id' => $formulas[array_rand($formulas)],
            ],
            [
                'name' => 'Type 48 - Deluxe',
                'sku' => 'SKU-008',
                'code' => 'PROD-008',
                'type_id' => $types[array_rand($types)],
                'price' => 420000000,
                'qty' => 0,
                'formula_id' => $formulas[array_rand($formulas)],
            ],
            [
                'name' => 'Type 55 - Executive',
                'sku' => 'SKU-009',
                'code' => 'PROD-009',
                'type_id' => $types[array_rand($types)],
                'price' => 500000000,
                'qty' => 0,
                'formula_id' => $formulas[array_rand($formulas)],
            ],
            [
                'name' => 'Type 70 - Luxury',
                'sku' => 'SKU-010',
                'code' => 'PROD-010',
                'type_id' => $types[array_rand($types)],
                'price' => 650000000,
                'qty' => 0,
                'formula_id' => $formulas[array_rand($formulas)],
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }

        $this->command->info('Created ' . count($products) . ' products successfully!');
    }
}
