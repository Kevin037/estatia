<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderDetail;
use App\Models\Project;
use App\Models\Supplier;
use App\Models\Material;
use App\Exports\PurchaseOrdersExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Maatwebsite\Excel\Facades\Excel;

class PurchaseOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = PurchaseOrder::with(['supplier', 'project'])
                ->select('purchase_orders.*');

            // Apply search filter
            if ($request->has('search') && $request->search['value']) {
                $search = $request->search['value'];
                $query->search($search);
            }

            // Apply status filter
            if ($request->filled('status')) {
                $query->byStatus($request->status);
            }

            // Apply date range filter
            if ($request->filled('start_date') && $request->filled('end_date')) {
                $query->dateRange($request->start_date, $request->end_date);
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->editColumn('no', function ($purchaseOrder) {
                    return $purchaseOrder->no ?? '-';
                })
                ->editColumn('supplier_id', function ($purchaseOrder) {
                    return $purchaseOrder->supplier ? $purchaseOrder->supplier->name : '-';
                })
                ->editColumn('dt', function ($purchaseOrder) {
                    return $purchaseOrder->dt ? $purchaseOrder->dt->format('d M Y') : '-';
                })
                ->editColumn('total', function ($purchaseOrder) {
                    return 'Rp ' . number_format($purchaseOrder->total, 0, ',', '.');
                })
                ->editColumn('status', function ($purchaseOrder) {
                    $colors = [
                        'pending' => 'bg-yellow-100 text-yellow-800',
                        'completed' => 'bg-green-100 text-green-800',
                    ];
                    $color = $colors[$purchaseOrder->status] ?? 'bg-gray-100 text-gray-800';
                    return '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ' . $color . '">' 
                           . ucfirst($purchaseOrder->status) . '</span>';
                })
                ->addColumn('actions', function ($purchaseOrder) {
                    return view('purchase-orders.partials.actions', compact('purchaseOrder'))->render();
                })
                ->rawColumns(['status', 'actions'])
                ->make(true);
        }

        return view('purchase-orders.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $projects = Project::orderBy('name')->get();
        $suppliers = Supplier::orderBy('name')->get();
        $materials = Material::with('supplier')->orderBy('name')->get();

        return view('purchase-orders.create', compact('projects', 'suppliers', 'materials'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'dt' => 'required|date',
            'supplier_id' => 'required|exists:suppliers,id',
            'materials' => 'required|array|min:1',
            'materials.*.material_id' => 'required|exists:materials,id',
            'materials.*.qty' => 'required|numeric|min:0.01',
        ], [
            'materials.required' => 'At least one material is required.',
            'materials.min' => 'At least one material is required.',
            'materials.*.material_id.required' => 'Material selection is required.',
            'materials.*.qty.required' => 'Quantity is required.',
            'materials.*.qty.min' => 'Quantity must be greater than 0.',
        ]);

        DB::beginTransaction();

        try {
            // Create purchase order with auto-generated number
            $purchaseOrder = PurchaseOrder::create([
                'no' => PurchaseOrder::generateNumber(),
                'project_id' => $request->project_id,
                'dt' => $request->dt,
                'supplier_id' => $request->supplier_id,
                'status' => $request->status ?? 'pending',
                'total' => 0, // Will be calculated below
            ]);

            $total = 0;

            // Create purchase order details and update material stock
            foreach ($request->materials as $item) {
                $material = Material::findOrFail($item['material_id']);
                
                // Create detail record
                PurchaseOrderDetail::create([
                    'purchase_order_id' => $purchaseOrder->id,
                    'material_id' => $item['material_id'],
                    'qty' => $item['qty'],
                ]);

                // Calculate subtotal (qty * price)
                $subtotal = $item['qty'] * $material->price;
                $total += $subtotal;

                // Update material stock (increase qty)
                $material->increment('qty', $item['qty']);
            }

            // Update purchase order total
            $purchaseOrder->update(['total' => $total]);

            DB::commit();

            return redirect()
                ->route('purchase-orders.index')
                ->with('success', 'Purchase Order created successfully. Transaction No: ' . $purchaseOrder->no);

        } catch (\Exception $e) {
            DB::rollback();
            
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to create Purchase Order: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load(['project', 'supplier', 'details.material']);
        
        return view('purchase-orders.show', compact('purchaseOrder'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load('details.material');
        $projects = Project::orderBy('name')->get();
        $suppliers = Supplier::orderBy('name')->get();
        $materials = Material::with('supplier')->orderBy('name')->get();

        return view('purchase-orders.edit', compact('purchaseOrder', 'projects', 'suppliers', 'materials'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PurchaseOrder $purchaseOrder)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'dt' => 'required|date',
            'supplier_id' => 'required|exists:suppliers,id',
            'materials' => 'required|array|min:1',
            'materials.*.material_id' => 'required|exists:materials,id',
            'materials.*.qty' => 'required|numeric|min:0.01',
        ], [
            'materials.required' => 'At least one material is required.',
            'materials.min' => 'At least one material is required.',
            'materials.*.material_id.required' => 'Material selection is required.',
            'materials.*.qty.required' => 'Quantity is required.',
            'materials.*.qty.min' => 'Quantity must be greater than 0.',
        ]);

        DB::beginTransaction();

        try {
            // Revert previous stock changes
            foreach ($purchaseOrder->details as $detail) {
                $material = Material::findOrFail($detail->material_id);
                $material->decrement('qty', $detail->qty);
            }

            // Delete old details
            $purchaseOrder->details()->delete();

            // Update purchase order basic info
            $purchaseOrder->update([
                'project_id' => $request->project_id,
                'dt' => $request->dt,
                'supplier_id' => $request->supplier_id,
                'status' => $request->status ?? $purchaseOrder->status,
            ]);

            $total = 0;

            // Create new details and update material stock
            foreach ($request->materials as $item) {
                $material = Material::findOrFail($item['material_id']);
                
                // Create detail record
                PurchaseOrderDetail::create([
                    'purchase_order_id' => $purchaseOrder->id,
                    'material_id' => $item['material_id'],
                    'qty' => $item['qty'],
                ]);

                // Calculate subtotal
                $subtotal = $item['qty'] * $material->price;
                $total += $subtotal;

                // Update material stock (increase qty)
                $material->increment('qty', $item['qty']);
            }

            // Update purchase order total
            $purchaseOrder->update(['total' => $total]);

            DB::commit();

            return redirect()
                ->route('purchase-orders.index')
                ->with('success', 'Purchase Order updated successfully.');

        } catch (\Exception $e) {
            DB::rollback();
            
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to update Purchase Order: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PurchaseOrder $purchaseOrder)
    {
        DB::beginTransaction();

        try {
            // Revert stock changes before deleting
            foreach ($purchaseOrder->details as $detail) {
                $material = Material::findOrFail($detail->material_id);
                $material->decrement('qty', $detail->qty);
            }

            // Delete details
            $purchaseOrder->details()->delete();

            // Delete purchase order
            $purchaseOrder->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Purchase Order deleted successfully.'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete Purchase Order: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export purchase orders to Excel
     */
    public function export(Request $request)
    {
        $startDate = $request->start_date;
        $endDate = $request->end_date;
        $status = $request->status;

        return Excel::download(
            new PurchaseOrdersExport($startDate, $endDate, $status), 
            'purchase-orders-' . date('Y-m-d-His') . '.xlsx'
        );
    }

    /**
     * Get materials by supplier (AJAX)
     */
    public function getMaterialsBySupplier(Request $request)
    {
        $materials = Material::where('supplier_id', $request->supplier_id)
            ->orderBy('name')
            ->get(['id', 'name', 'price', 'qty']);

        return response()->json($materials);
    }
}
