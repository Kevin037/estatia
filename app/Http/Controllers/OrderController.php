<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Customer;
use App\Models\Project;
use App\Models\Cluster;
use App\Models\Unit;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Exports\OrdersExport;
use Maatwebsite\Excel\Facades\Excel;

class OrderController extends Controller
{
    /**
     * Display a listing of orders with DataTables
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Order::with(['customer', 'project', 'cluster', 'unit']);

            // Project filter
            if ($request->filled('project_id')) {
                $query->where('project_id', $request->project_id);
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('dt_formatted', function ($order) {
                    return $order->dt ? $order->dt->format('d M Y') : '-';
                })
                ->addColumn('customer_name', function ($order) {
                    return $order->customer->name ?? '-';
                })
                ->addColumn('project_name', function ($order) {
                    return $order->project->name ?? '-';
                })
                ->addColumn('cluster_name', function ($order) {
                    return $order->cluster->name ?? '-';
                })
                ->addColumn('unit_no', function ($order) {
                    return $order->unit->no ?? '-';
                })
                ->addColumn('status', function ($order) {
                    $colors = [
                        'pending' => 'bg-yellow-100 text-yellow-800',
                        'completed' => 'bg-green-100 text-green-800',
                    ];
                    $color = $colors[$order->status] ?? 'bg-gray-100 text-gray-800';
                    return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ' . $color . '">' . 
                           ucfirst($order->status) . '</span>';
                })
                ->addColumn('action', function ($order) {
                    return view('orders.actions', compact('order'));
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }

        // Get all projects for filter dropdown
        $projects = Project::orderBy('name')->get();

        return view('orders.index', compact('projects'));
    }

    /**
     * Show the form for creating a new order
     */
    public function create()
    {
        $customers = Customer::orderBy('name')->get();
        $projects = Project::orderBy('name')->get();
        
        return view('orders.create', compact('customers', 'projects'));
    }

    /**
     * Store a newly created order in storage
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'dt' => 'required|date',
            'customer_id' => 'required|exists:customers,id',
            'project_id' => 'required|exists:projects,id',
            'cluster_id' => 'required|exists:clusters,id',
            'unit_id' => 'required|exists:units,id',
            'status' => 'required|in:pending,completed',
            'notes' => 'nullable|string',
        ]);

        // Generate order number
        $validated['no'] = Order::generateNumber();
        $validated['total'] = 0; // Set default or calculate from unit price

        // Get unit price if available
        if ($request->unit_id) {
            $unit = Unit::find($request->unit_id);
            if ($unit) {
                $validated['total'] = $unit->price;
            }
        }

        $order = Order::create($validated);

        return redirect()
            ->route('orders.show', $order)
            ->with('success', 'Order created successfully!');
    }

    /**
     * Display the specified order
     */
    public function show(Order $order)
    {
        // Eager load relationships
        $order->load([
            'customer',
            'project',
            'cluster',
            'unit.product.type',
            'unit.product.productPhotos',
            'unit.unitPhotos'
        ]);

        return view('orders.show', compact('order'));
    }

    /**
     * Show the form for editing the specified order
     */
    public function edit(Order $order)
    {
        $customers = Customer::orderBy('name')->get();
        $projects = Project::orderBy('name')->get();
        
        // Get clusters for the selected project
        $clusters = $order->project_id 
            ? Cluster::where('project_id', $order->project_id)->orderBy('name')->get()
            : collect();
        
        // Get units for the selected cluster
        $units = $order->cluster_id
            ? Unit::where('cluster_id', $order->cluster_id)->orderBy('no')->get()
            : collect();

        // Load unit details for preview
        if ($order->unit_id) {
            $order->load([
                'unit.product.type',
                'unit.product.productPhotos',
                'unit.unitPhotos'
            ]);
        }

        return view('orders.edit', compact('order', 'customers', 'projects', 'clusters', 'units'));
    }

    /**
     * Update the specified order in storage
     */
    public function update(Request $request, Order $order)
    {
        $validated = $request->validate([
            'dt' => 'required|date',
            'customer_id' => 'required|exists:customers,id',
            'project_id' => 'required|exists:projects,id',
            'cluster_id' => 'required|exists:clusters,id',
            'unit_id' => 'required|exists:units,id',
            'status' => 'required|in:pending,completed',
            'notes' => 'nullable|string',
        ]);

        // Update total if unit changed
        if ($request->unit_id != $order->unit_id) {
            $unit = Unit::find($request->unit_id);
            if ($unit) {
                $validated['total'] = $unit->price;
            }
        }

        $order->update($validated);

        return redirect()
            ->route('orders.show', $order)
            ->with('success', 'Order updated successfully!');
    }

    /**
     * Remove the specified order from storage
     */
    public function destroy(Order $order)
    {
        $order->delete();

        return redirect()
            ->route('orders.index')
            ->with('success', 'Order deleted successfully!');
    }

    /**
     * Get clusters by project (Ajax)
     */
    public function getClusters(Request $request)
    {
        $clusters = Cluster::where('project_id', $request->project_id)
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json($clusters);
    }

    /**
     * Get units by cluster (Ajax)
     */
    public function getUnits(Request $request)
    {
        $units = Unit::where('cluster_id', $request->cluster_id)
            ->with(['product.type'])
            ->orderBy('no')
            ->get();

        return response()->json($units);
    }

    /**
     * Get unit details (Ajax)
     */
    public function getUnitDetails(Request $request)
    {
        $unit = Unit::with([
            'product.type',
            'product.formula',
            'product.productPhotos',
            'cluster.project',
            'sales',
            'unitPhotos'
        ])->find($request->unit_id);

        if (!$unit) {
            return response()->json(['error' => 'Unit not found'], 404);
        }

        // Format the response
        $response = [
            'id' => $unit->id,
            'name' => $unit->name,
            'no' => $unit->no,
            'price' => $unit->price,
            'price_formatted' => 'Rp ' . number_format($unit->price, 0, ',', '.'),
            'status' => $unit->status,
            'status_label' => ucfirst(str_replace('_', ' ', $unit->status)),
            'description' => $unit->desc,
            'facilities' => $unit->facilities,
            'cluster' => [
                'name' => $unit->cluster->name ?? '-',
                'facilities' => $unit->cluster->facilities ?? '-',
            ],
            'project' => [
                'name' => $unit->cluster->project->name ?? '-',
            ],
            'product' => [
                'type' => $unit->product->type->name ?? '-',
                'code' => $unit->product->sku ?? '-',
                'land_area' => $unit->product->type->land_area ?? '-',
                'building_area' => $unit->product->type->building_area ?? '-',
            ],
            'product_photos' => $unit->product->productPhotos->map(function($photo) {
                return [
                    'url' => $photo->photo_url,
                    'name' => $photo->name ?? ''
                ];
            }),
            'unit_photos' => $unit->unitPhotos->map(function($photo) {
                return [
                    'url' => $photo->photo_url,
                    'name' => $photo->name ?? ''
                ];
            }),
            'sales' => $unit->sales ? [
                'name' => $unit->sales->name,
                'phone' => $unit->sales->phone,
            ] : null,
        ];

        return response()->json($response);
    }

    /**
     * Export orders to Excel
     */
    public function export(Request $request)
    {
        return Excel::download(
            new OrdersExport($request->project_id),
            'orders_' . now()->format('Y-m-d_His') . '.xlsx'
        );
    }
}
