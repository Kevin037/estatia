<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $suppliers = [
            [
                'name' => 'PT Semen Indonesia',
                'phone' => '02187654321',
            ],
            [
                'name' => 'CV Baja Perkasa',
                'phone' => '02187123456',
            ],
            [
                'name' => 'UD Kayu Jati Murni',
                'phone' => '02131234567',
            ],
            [
                'name' => 'Toko Bangunan Sentosa',
                'phone' => '02156781234',
            ],
            [
                'name' => 'PT Aluminium Indah',
                'phone' => '02198765432',
            ],
        ];

        foreach ($suppliers as $supplier) {
            Supplier::create($supplier);
        }

        $this->command->info('Suppliers seeded successfully.');
    }
}
