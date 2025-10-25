<?php

namespace Database\Seeders;

use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderDetail;
use App\Models\Project;
use App\Models\Supplier;
use App\Models\Material;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PurchaseOrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure dependencies exist
        if (Project::count() === 0) {
            $this->call(ProjectSeeder::class);
        }
        if (Supplier::count() === 0) {
            $this->call(SupplierSeeder::class);
        }
        if (Material::count() === 0) {
            $this->call(MaterialSeeder::class);
        }

        $projects = Project::all();
        $suppliers = Supplier::all();

        // Create 8 sample purchase orders
        $purchaseOrders = [
            [
                'project' => $projects[0] ?? Project::first(),
                'supplier' => $suppliers[0] ?? Supplier::first(),
                'dt' => now()->subDays(30),
                'status' => 'completed',
                'materials' => [
                    ['material_name' => 'Semen Portland Type I', 'qty' => 100],
                    ['material_name' => 'Semen Portland Type II', 'qty' => 50],
                ],
            ],
            [
                'project' => $projects[1] ?? Project::first(),
                'supplier' => $suppliers[1] ?? Supplier::skip(1)->first(),
                'dt' => now()->subDays(25),
                'status' => 'completed',
                'materials' => [
                    ['material_name' => 'Besi Beton Polos 8mm', 'qty' => 200],
                    ['material_name' => 'Besi Beton Ulir 10mm', 'qty' => 150],
                    ['material_name' => 'Besi Hollow 4x4', 'qty' => 80],
                ],
            ],
            [
                'project' => $projects[0] ?? Project::first(),
                'supplier' => $suppliers[2] ?? Supplier::skip(2)->first(),
                'dt' => now()->subDays(20),
                'status' => 'completed',
                'materials' => [
                    ['material_name' => 'Kayu Meranti', 'qty' => 50],
                    ['material_name' => 'Triplek 12mm', 'qty' => 30],
                ],
            ],
            [
                'project' => $projects[1] ?? Project::first(),
                'supplier' => $suppliers[3] ?? Supplier::skip(3)->first(),
                'dt' => now()->subDays(15),
                'status' => 'completed',
                'materials' => [
                    ['material_name' => 'Pasir Beton', 'qty' => 20],
                    ['material_name' => 'Batu Split', 'qty' => 15],
                    ['material_name' => 'Batako Press', 'qty' => 500],
                ],
            ],
            [
                'project' => $projects[2] ?? Project::first(),
                'supplier' => $suppliers[0] ?? Supplier::first(),
                'dt' => now()->subDays(10),
                'status' => 'pending',
                'materials' => [
                    ['material_name' => 'Semen Portland Type I', 'qty' => 75],
                ],
            ],
            [
                'project' => $projects[3] ?? Project::first(),
                'supplier' => $suppliers[4] ?? Supplier::skip(4)->first(),
                'dt' => now()->subDays(7),
                'status' => 'pending',
                'materials' => [
                    ['material_name' => 'Kusen Aluminium', 'qty' => 40],
                    ['material_name' => 'Kaca Polos 5mm', 'qty' => 60],
                ],
            ],
            [
                'project' => $projects[1] ?? Project::first(),
                'supplier' => $suppliers[3] ?? Supplier::skip(3)->first(),
                'dt' => now()->subDays(5),
                'status' => 'pending',
                'materials' => [
                    ['material_name' => 'Cat Tembok Interior', 'qty' => 25],
                    ['material_name' => 'Batako Press', 'qty' => 300],
                ],
            ],
            [
                'project' => $projects[0] ?? Project::first(),
                'supplier' => $suppliers[1] ?? Supplier::skip(1)->first(),
                'dt' => now()->subDays(2),
                'status' => 'pending',
                'materials' => [
                    ['material_name' => 'Besi Beton Ulir 10mm', 'qty' => 100],
                    ['material_name' => 'Besi Hollow 4x4', 'qty' => 50],
                ],
            ],
        ];

        foreach ($purchaseOrders as $index => $poData) {
            DB::beginTransaction();

            try {
                // Create purchase order
                $po = PurchaseOrder::create([
                    'no' => 'PO-' . str_pad($index + 1, 6, '0', STR_PAD_LEFT),
                    'project_id' => $poData['project']->id,
                    'supplier_id' => $poData['supplier']->id,
                    'dt' => $poData['dt'],
                    'status' => $poData['status'],
                    'total' => 0,
                ]);

                $total = 0;

                // Create details and update stock
                foreach ($poData['materials'] as $materialData) {
                    // Find material by partial name match
                    $material = Material::where('name', 'like', '%' . $materialData['material_name'] . '%')
                        ->where('supplier_id', $poData['supplier']->id)
                        ->first();

                    if ($material) {
                        // Create detail
                        PurchaseOrderDetail::create([
                            'purchase_order_id' => $po->id,
                            'material_id' => $material->id,
                            'qty' => $materialData['qty'],
                        ]);

                        // Calculate subtotal
                        $subtotal = $materialData['qty'] * $material->price;
                        $total += $subtotal;

                        // Update material stock
                        $material->increment('qty', $materialData['qty']);
                    }
                }

                // Update purchase order total
                $po->update(['total' => $total]);

                DB::commit();

                $this->command->info("Purchase Order {$po->no} created successfully.");

            } catch (\Exception $e) {
                DB::rollback();
                $this->command->error("Failed to create Purchase Order: " . $e->getMessage());
            }
        }

        $this->command->info('All Purchase Orders seeded successfully.');
    }
}
