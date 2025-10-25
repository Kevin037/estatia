<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\Material;
use App\Exports\SupplierExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Supplier::query();

            // Apply date range filter if provided
            if ($request->filled('start_date') && $request->filled('end_date')) {
                $query->whereBetween('created_at', [
                    $request->start_date . ' 00:00:00',
                    $request->end_date . ' 23:59:59'
                ]);
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('phone', function ($supplier) {
                    $phoneNumber = preg_replace('/[^0-9]/', '', $supplier->phone);
                    // Add country code if not present (assuming Indonesia +62)
                    if (!str_starts_with($phoneNumber, '62')) {
                        if (str_starts_with($phoneNumber, '0')) {
                            $phoneNumber = '62' . substr($phoneNumber, 1);
                        } else {
                            $phoneNumber = '62' . $phoneNumber;
                        }
                    }
                    $whatsappUrl = 'https://wa.me/' . $phoneNumber;
                    
                    return '<a href="' . $whatsappUrl . '" target="_blank" class="inline-flex items-center gap-2 text-emerald-600 hover:text-emerald-700 transition-colors">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                        </svg>
                        <span>' . e($supplier->phone) . '</span>
                    </a>';
                })
                ->addColumn('action', function ($supplier) {
                    return view('suppliers.partials.actions', compact('supplier'))->render();
                })
                ->rawColumns(['phone', 'action'])
                ->make(true);
        }

        return view('suppliers.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Get all materials (since supplier_id is required in existing schema, we show all)
        $availableMaterials = Material::all();
        return view('suppliers.create', compact('availableMaterials'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'material_ids' => ['nullable', 'array'],
            'material_ids.*' => ['exists:materials,id'],
        ]);

        $supplier = Supplier::create([
            'name' => $data['name'],
            'phone' => $data['phone'],
        ]);

        // Assign selected materials to this supplier
        if (!empty($data['material_ids'])) {
            Material::whereIn('id', $data['material_ids'])
                ->update(['supplier_id' => $supplier->id]);
        }

        return redirect()->route('suppliers.index')
            ->with('success', 'Supplier created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Supplier $supplier)
    {
        // Get all materials (since supplier_id is required in existing schema)
        $availableMaterials = Material::all();
        
        $assignedMaterialIds = $supplier->materials->pluck('id')->toArray();
        
        return view('suppliers.edit', compact('supplier', 'availableMaterials', 'assignedMaterialIds'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Supplier $supplier)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'material_ids' => ['nullable', 'array'],
            'material_ids.*' => ['exists:materials,id'],
        ]);

        $supplier->update([
            'name' => $data['name'],
            'phone' => $data['phone'],
        ]);

        // Remove this supplier from all previous materials
        Material::where('supplier_id', $supplier->id)
            ->update(['supplier_id' => null]);

        // Assign selected materials to this supplier
        if (!empty($data['material_ids'])) {
            Material::whereIn('id', $data['material_ids'])
                ->update(['supplier_id' => $supplier->id]);
        }

        return redirect()->route('suppliers.index')
            ->with('success', 'Supplier updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Supplier $supplier)
    {
        try {
            // Note: In the existing schema, supplier_id is NOT NULL,
            // so we cannot just unassign materials. We'll keep them assigned.
            // In a production scenario, you'd need to decide the business logic:
            // either reassign to another supplier or prevent deletion if materials exist.
            
            if ($supplier->materials()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete supplier with assigned materials. Please reassign materials first.'
                ], 400);
            }
                
            $supplier->delete();

            return response()->json([
                'success' => true,
                'message' => 'Supplier deleted successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete supplier: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export suppliers to Excel.
     */
    public function export(Request $request)
    {
        try {
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');
            
            $fileName = 'suppliers_' . date('YmdHis') . '.xlsx';
            
            return Excel::download(
                new SupplierExport($startDate, $endDate), 
                $fileName
            );
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Failed to export suppliers: ' . $e->getMessage());
        }
    }
}
