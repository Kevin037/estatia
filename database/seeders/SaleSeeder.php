<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Sale;

class SaleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sales = [
            [
                'name' => 'Ahmad Fauzi',
                'phone' => '081234567890',
            ],
            [
                'name' => 'Siti Nurhaliza',
                'phone' => '081298765432',
            ],
            [
                'name' => 'Budi Santoso',
                'phone' => '081345678901',
            ],
            [
                'name' => 'Dewi Lestari',
                'phone' => '081456789012',
            ],
            [
                'name' => 'Eko Prasetyo',
                'phone' => '081567890123',
            ],
            [
                'name' => 'Fitri Handayani',
                'phone' => '081678901234',
            ],
            [
                'name' => 'Gunawan Wijaya',
                'phone' => '081789012345',
            ],
            [
                'name' => 'Hani Rahmawati',
                'phone' => '081890123456',
            ],
        ];

        foreach ($sales as $sale) {
            Sale::create($sale);
        }
    }
}
