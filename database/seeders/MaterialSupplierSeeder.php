<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Material;
use App\Models\Supplier;

class MaterialSupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create suppliers
        $supplier1 = Supplier::create([
            'name' => 'PT Beton Indonesia',
            'phone' => '081234567890',
        ]);

        $supplier2 = Supplier::create([
            'name' => 'CV Kayu Jati Makmur',
            'phone' => '081298765432',
        ]);

        // Create a third supplier for future assignments
        $supplier3 = Supplier::create([
            'name' => 'Toko Bangunan Sumber Rejeki',
            'phone' => '081387654321',
        ]);

        // Create materials assigned to supplier1
        Material::create([
            'name' => 'Besi Beton 10mm',
            'qty' => 200,
            'price' => 85000,
            'supplier_id' => $supplier1->id,
        ]);

        Material::create([
            'name' => 'Ready Mix K-250',
            'qty' => 50,
            'price' => 850000,
            'supplier_id' => $supplier1->id,
        ]);

        Material::create([
            'name' => 'Semen Portland 50kg',
            'qty' => 150,
            'price' => 65000,
            'supplier_id' => $supplier1->id,
        ]);

        // Create materials assigned to supplier2
        Material::create([
            'name' => 'Kayu Meranti 4x6',
            'qty' => 100,
            'price' => 45000,
            'supplier_id' => $supplier2->id,
        ]);

        Material::create([
            'name' => 'Triplek 9mm',
            'qty' => 75,
            'price' => 125000,
            'supplier_id' => $supplier2->id,
        ]);

        Material::create([
            'name' => 'Pipa PVC 4 inch',
            'qty' => 90,
            'price' => 95000,
            'supplier_id' => $supplier2->id,
        ]);

        // Create materials assigned to supplier3
        Material::create([
            'name' => 'Pasir Cor per m³',
            'qty' => 80,
            'price' => 250000,
            'supplier_id' => $supplier3->id,
        ]);

        Material::create([
            'name' => 'Batu Split per m³',
            'qty' => 60,
            'price' => 300000,
            'supplier_id' => $supplier3->id,
        ]);

        Material::create([
            'name' => 'Cat Tembok Avian 5kg',
            'qty' => 120,
            'price' => 175000,
            'supplier_id' => $supplier3->id,
        ]);

        Material::create([
            'name' => 'Genteng Keramik',
            'qty' => 300,
            'price' => 8500,
            'supplier_id' => $supplier3->id,
        ]);
    }
}

