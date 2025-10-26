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
                ->addColumn('customer_name', function ($order) {
                    $phoneNumber = preg_replace('/[^0-9]/', '', $order->customer->phone);
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
                        <span>' . e($order->customer->name ?? '-') . '</span>
                    </a>';
                })
                ->addColumn('dt_formatted', function ($order) {
                    return $order->dt ? $order->dt->format('d M Y') : '-';
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
                ->rawColumns(['status', 'action','customer_name'])
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
