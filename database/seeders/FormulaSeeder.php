<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Formula;
use App\Models\FormulaDetail;
use App\Models\Material;

class FormulaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $materials = Material::all();
        
        if ($materials->count() < 3) {
            $this->command->warn('Not enough materials in database. Please seed materials first.');
            return;
        }

        // Formula 1: Basic Mix
        $formula1 = Formula::create([
            'code' => 'F-001',
            'name' => 'Basic Concrete Mix',
            'total' => 0,
        ]);

        $material1 = $materials->random();
        $material2 = $materials->where('id', '!=', $material1->id)->random();

        FormulaDetail::create([
            'formula_id' => $formula1->id,
            'material_id' => $material1->id,
            'qty' => 10.5,
        ]);

        FormulaDetail::create([
            'formula_id' => $formula1->id,
            'material_id' => $material2->id,
            'qty' => 5.25,
        ]);

        $total1 = ($material1->price * 10.5) + ($material2->price * 5.25);
        $formula1->update(['total' => $total1]);

        // Formula 2: Premium Mix
        $formula2 = Formula::create([
            'code' => 'F-002',
            'name' => 'Premium Building Material',
            'total' => 0,
        ]);

        $material3 = $materials->whereNotIn('id', [$material1->id, $material2->id])->random();

        FormulaDetail::create([
            'formula_id' => $formula2->id,
            'material_id' => $material2->id,
            'qty' => 8.0,
        ]);

        FormulaDetail::create([
            'formula_id' => $formula2->id,
            'material_id' => $material3->id,
            'qty' => 12.5,
        ]);

        $total2 = ($material2->price * 8.0) + ($material3->price * 12.5);
        $formula2->update(['total' => $total2]);

        // Formula 3: Standard Mix
        $formula3 = Formula::create([
            'code' => 'F-003',
            'name' => 'Standard Foundation Mix',
            'total' => 0,
        ]);

        FormulaDetail::create([
            'formula_id' => $formula3->id,
            'material_id' => $material1->id,
            'qty' => 15.0,
        ]);

        FormulaDetail::create([
            'formula_id' => $formula3->id,
            'material_id' => $material3->id,
            'qty' => 7.5,
        ]);

        FormulaDetail::create([
            'formula_id' => $formula3->id,
            'material_id' => $material2->id,
            'qty' => 3.0,
        ]);

        $total3 = ($material1->price * 15.0) + ($material3->price * 7.5) + ($material2->price * 3.0);
        $formula3->update(['total' => $total3]);

        $this->command->info('Created 3 formulas with details successfully!');
    }
}
