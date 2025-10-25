<?php

namespace App\Http\Controllers;

use App\Models\Material;
use App\Models\Supplier;
use App\Exports\MaterialExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class MaterialController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Material::with('supplier');

            // Apply date range filter if provided
            if ($request->filled('start_date') && $request->filled('end_date')) {
                $query->whereBetween('created_at', [
                    $request->start_date . ' 00:00:00',
                    $request->end_date . ' 23:59:59'
                ]);
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('supplier_name', function ($material) {
                    return $material->supplier ? $material->supplier->name : '-';
                })
                ->addColumn('formatted_price', function ($material) {
                    return 'Rp ' . number_format($material->price, 0, ',', '.');
                })
                ->addColumn('action', function ($material) {
                    return view('materials.partials.actions', compact('material'))->render();
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('materials.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('materials.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
        ]);

        // Stock defaults to 0 in migration
        Material::create($data);

        return redirect()->route('materials.index')
            ->with('success', 'Material created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Material $material)
    {
        return view('materials.edit', compact('material'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Material $material)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
        ]);

        $material->update($data);

        return redirect()->route('materials.index')
            ->with('success', 'Material updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Material $material)
    {
        try {
            $material->delete();

            return response()->json([
                'success' => true,
                'message' => 'Material deleted successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete material: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export materials to Excel.
     */
    public function export(Request $request)
    {
        try {
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');
            
            $fileName = 'materials_' . date('YmdHis') . '.xlsx';
            
            return Excel::download(
                new MaterialExport($startDate, $endDate), 
                $fileName
            );
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Failed to export materials: ' . $e->getMessage());
        }
    }
}
