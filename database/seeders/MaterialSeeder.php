<?php

namespace Database\Seeders;

use App\Models\Material;
use App\Models\Supplier;
use Illuminate\Database\Seeder;

class MaterialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure suppliers exist
        if (Supplier::count() === 0) {
            $this->call(SupplierSeeder::class);
        }

        $suppliers = Supplier::all();

        $materials = [
            // Cement from PT Semen Indonesia
            [
                'name' => 'Semen Portland Type I - 50kg',
                'price' => 65000,
                'qty' => 500,
                'supplier_id' => $suppliers[0]->id ?? 1,
            ],
            [
                'name' => 'Semen Portland Type II - 50kg',
                'price' => 68000,
                'qty' => 300,
                'supplier_id' => $suppliers[0]->id ?? 1,
            ],
            // Steel from CV Baja Perkasa
            [
                'name' => 'Besi Beton Polos 8mm - 12m',
                'price' => 85000,
                'qty' => 1000,
                'supplier_id' => $suppliers[1]->id ?? 2,
            ],
            [
                'name' => 'Besi Beton Ulir 10mm - 12m',
                'price' => 125000,
                'qty' => 800,
                'supplier_id' => $suppliers[1]->id ?? 2,
            ],
            [
                'name' => 'Besi Hollow 4x4 - 6m',
                'price' => 165000,
                'qty' => 400,
                'supplier_id' => $suppliers[1]->id ?? 2,
            ],
            // Wood from UD Kayu Jati Murni
            [
                'name' => 'Kayu Meranti Kelas A - 4m',
                'price' => 450000,
                'qty' => 200,
                'supplier_id' => $suppliers[2]->id ?? 3,
            ],
            [
                'name' => 'Triplek 12mm - 122x244cm',
                'price' => 185000,
                'qty' => 150,
                'supplier_id' => $suppliers[2]->id ?? 3,
            ],
            // Building materials from Toko Bangunan Sentosa
            [
                'name' => 'Pasir Beton - 1 Kubik',
                'price' => 350000,
                'qty' => 100,
                'supplier_id' => $suppliers[3]->id ?? 4,
            ],
            [
                'name' => 'Batu Split - 1 Kubik',
                'price' => 425000,
                'qty' => 80,
                'supplier_id' => $suppliers[3]->id ?? 4,
            ],
            [
                'name' => 'Batako Press - per pcs',
                'price' => 3500,
                'qty' => 5000,
                'supplier_id' => $suppliers[3]->id ?? 4,
            ],
            [
                'name' => 'Cat Tembok Interior - 25kg',
                'price' => 385000,
                'qty' => 120,
                'supplier_id' => $suppliers[3]->id ?? 4,
            ],
            // Aluminum from PT Aluminium Indah
            [
                'name' => 'Kusen Aluminium - 6m',
                'price' => 285000,
                'qty' => 250,
                'supplier_id' => $suppliers[4]->id ?? 5,
            ],
            [
                'name' => 'Kaca Polos 5mm - m2',
                'price' => 125000,
                'qty' => 300,
                'supplier_id' => $suppliers[4]->id ?? 5,
            ],
        ];

        foreach ($materials as $material) {
            Material::create($material);
        }

        $this->command->info('Materials seeded successfully.');
    }
}
