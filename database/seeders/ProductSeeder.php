<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Formula;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get some formula IDs
        $formulas = Formula::pluck('id')->toArray();

        $products = [
            [
                'name' => 'Premium Office Chair',
                'sku' => 'PROD-001',
                'price' => 2500000,
                'qty' => 0,
                'formula_id' => !empty($formulas) ? $formulas[array_rand($formulas)] : null,
            ],
            [
                'name' => 'Executive Desk',
                'sku' => 'PROD-002',
                'price' => 4500000,
                'qty' => 0,
                'formula_id' => !empty($formulas) ? $formulas[array_rand($formulas)] : null,
            ],
            [
                'name' => 'Conference Table',
                'sku' => 'PROD-003',
                'price' => 8500000,
                'qty' => 0,
                'formula_id' => !empty($formulas) ? $formulas[array_rand($formulas)] : null,
            ],
            [
                'name' => 'Filing Cabinet',
                'sku' => 'PROD-004',
                'price' => 1500000,
                'qty' => 0,
                'formula_id' => !empty($formulas) ? $formulas[array_rand($formulas)] : null,
            ],
            [
                'name' => 'Bookshelf Unit',
                'sku' => 'PROD-005',
                'price' => 3200000,
                'qty' => 0,
                'formula_id' => !empty($formulas) ? $formulas[array_rand($formulas)] : null,
            ],
            [
                'name' => 'Reception Counter',
                'sku' => 'PROD-006',
                'price' => 5500000,
                'qty' => 0,
                'formula_id' => !empty($formulas) ? $formulas[array_rand($formulas)] : null,
            ],
            [
                'name' => 'Storage Cabinet',
                'sku' => 'PROD-007',
                'price' => 2800000,
                'qty' => 0,
                'formula_id' => !empty($formulas) ? $formulas[array_rand($formulas)] : null,
            ],
            [
                'name' => 'Meeting Chair',
                'sku' => 'PROD-008',
                'price' => 1200000,
                'qty' => 0,
                'formula_id' => !empty($formulas) ? $formulas[array_rand($formulas)] : null,
            ],
            [
                'name' => 'Display Cabinet',
                'sku' => 'PROD-009',
                'price' => 3800000,
                'qty' => 0,
                'formula_id' => !empty($formulas) ? $formulas[array_rand($formulas)] : null,
            ],
            [
                'name' => 'Workstation Desk',
                'sku' => 'PROD-010',
                'price' => 6500000,
                'qty' => 0,
                'formula_id' => !empty($formulas) ? $formulas[array_rand($formulas)] : null,
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
