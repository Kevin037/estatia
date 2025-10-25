<?php

namespace App\Http\Controllers;

use App\Models\Formula;
use App\Models\FormulaDetail;
use App\Models\Material;
use App\Exports\FormulaExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class FormulaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Formula::query();

            // Apply date range filter if provided
            if ($request->filled('start_date') && $request->filled('end_date')) {
                $query->whereBetween('created_at', [
                    $request->start_date . ' 00:00:00',
                    $request->end_date . ' 23:59:59'
                ]);
            }

            // Apply search filter
            if ($request->filled('search_name')) {
                $query->where('name', 'like', '%' . $request->search_name . '%');
            }

            if ($request->filled('search_code')) {
                $query->where('code', 'like', '%' . $request->search_code . '%');
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->editColumn('code', function ($formula) {
                    return '<span class="font-semibold text-gray-900">' . $formula->code . '</span>';
                })
                ->editColumn('total', function ($formula) {
                    return '<span class="font-medium text-emerald-600">Rp ' . number_format($formula->total, 0, ',', '.') . '</span>';
                })
                ->addColumn('action', function ($formula) {
                    return view('formulas.partials.actions', compact('formula'))->render();
                })
                ->rawColumns(['code', 'total', 'action'])
                ->make(true);
        }

        return view('formulas.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $materials = Material::orderBy('name')->get();
        return view('formulas.create', compact('materials'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:255', 'unique:formulas,code'],
            'name' => ['required', 'string', 'max:255'],
            'material_ids' => ['required', 'array', 'min:1'],
            'material_ids.*' => ['required', 'exists:materials,id'],
            'quantities' => ['required', 'array', 'min:1'],
            'quantities.*' => ['required', 'numeric', 'min:0.01'],
        ], [
            'material_ids.required' => 'Please add at least one material.',
            'material_ids.min' => 'Please add at least one material.',
        ]);

        try {
            DB::beginTransaction();

            // Calculate total from material prices and quantities
            $total = 0;
            foreach ($validated['material_ids'] as $index => $materialId) {
                $material = Material::find($materialId);
                $qty = $validated['quantities'][$index];
                $total += ($material->price * $qty);
            }

            // Create formula
            $formula = Formula::create([
                'code' => $validated['code'],
                'name' => $validated['name'],
                'total' => $total,
            ]);

            // Create formula details
            foreach ($validated['material_ids'] as $index => $materialId) {
                FormulaDetail::create([
                    'formula_id' => $formula->id,
                    'material_id' => $materialId,
                    'qty' => $validated['quantities'][$index],
                ]);
            }

            DB::commit();

            return redirect()
                ->route('formulas.index')
                ->with('success', 'Formula created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to create formula: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Formula $formula)
    {
        $formula->load('details.material');
        return view('formulas.show', compact('formula'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Formula $formula)
    {
        $materials = Material::orderBy('name')->get();
        $formula->load('details.material');
        return view('formulas.edit', compact('formula', 'materials'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Formula $formula)
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:255', 'unique:formulas,code,' . $formula->id],
            'name' => ['required', 'string', 'max:255'],
            'material_ids' => ['required', 'array', 'min:1'],
            'material_ids.*' => ['required', 'exists:materials,id'],
            'quantities' => ['required', 'array', 'min:1'],
            'quantities.*' => ['required', 'numeric', 'min:0.01'],
        ], [
            'material_ids.required' => 'Please add at least one material.',
            'material_ids.min' => 'Please add at least one material.',
        ]);

        try {
            DB::beginTransaction();

            // Calculate total from material prices and quantities
            $total = 0;
            foreach ($validated['material_ids'] as $index => $materialId) {
                $material = Material::find($materialId);
                $qty = $validated['quantities'][$index];
                $total += ($material->price * $qty);
            }

            // Update formula
            $formula->update([
                'code' => $validated['code'],
                'name' => $validated['name'],
                'total' => $total,
            ]);

            // Delete old details and create new ones
            $formula->details()->delete();
            
            foreach ($validated['material_ids'] as $index => $materialId) {
                FormulaDetail::create([
                    'formula_id' => $formula->id,
                    'material_id' => $materialId,
                    'qty' => $validated['quantities'][$index],
                ]);
            }

            DB::commit();

            return redirect()
                ->route('formulas.index')
                ->with('success', 'Formula updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to update formula: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Formula $formula)
    {
        try {
            $formula->delete(); // Details will be cascade deleted

            return response()->json([
                'success' => true,
                'message' => 'Formula deleted successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete formula: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export formulas to Excel.
     */
    public function export(Request $request)
    {
        try {
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');
            
            $fileName = 'formulas_' . date('YmdHis') . '.xlsx';
            
            return Excel::download(
                new FormulaExport($startDate, $endDate), 
                $fileName
            );
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Failed to export formulas: ' . $e->getMessage());
        }
    }
}
