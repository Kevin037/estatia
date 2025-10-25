<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Land;
use App\Models\Contractor;
use App\Models\Milestone;
use App\Models\Product;
use App\Models\Cluster;
use App\Models\Unit;
use App\Models\ProjectContractor;
use App\Models\ProjectMilestone;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderDetail;
use App\Models\Material;
use App\Models\Supplier;
use App\Exports\ProjectsExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Maatwebsite\Excel\Facades\Excel;

class ProjectController extends Controller
{
    /**
     * Display a listing of projects with DataTables
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Project::with(['land', 'clusters', 'units', 'contractors'])
                ->select('projects.*');

            // Apply filters
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('start_date')) {
                $query->whereDate('dt_start', '>=', $request->start_date);
            }

            if ($request->filled('end_date')) {
                $query->whereDate('dt_end', '<=', $request->end_date);
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('dt_start', function ($project) {
                    return $project->dt_start->format('d M Y');
                })
                ->addColumn('customer', function ($project) {
                    return $project->land ? $project->land->address : '-';
                })
                ->addColumn('product_count', function ($project) {
                    return $project->units()->distinct('product_id')->count('product_id');
                })
                ->addColumn('status', function ($project) {
                    $badges = [
                        'pending' => '<span class="badge badge-warning">Pending</span>',
                        'in_progress' => '<span class="badge badge-info">In Progress</span>',
                        'completed' => '<span class="badge badge-success">Completed</span>',
                    ];
                    return $badges[$project->status] ?? '<span class="badge badge-secondary">Unknown</span>';
                })
                ->addColumn('actions', function ($project) {
                    return view('projects.partials.actions', compact('project'))->render();
                })
                ->rawColumns(['status', 'actions'])
                ->make(true);
        }

        return view('projects.index');
    }

    /**
     * Show the form for creating a new project
     */
    public function create()
    {
        $lands = Land::all();
        $contractors = Contractor::all();
        $milestones = Milestone::all();
        $products = Product::with('type')->get();

        return view('projects.create', compact('lands', 'contractors', 'milestones', 'products'));
    }

    /**
     * Store a newly created project in storage
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'land_id' => 'required|exists:lands,id',
            'dt_start' => 'required|date',
            'dt_end' => 'required|date|after_or_equal:dt_start',
            'status' => 'required|in:pending,in_progress,completed',
            'contractors' => 'required|array|min:1',
            'contractors.*' => 'required|exists:contractors,id',
            'milestones' => 'required|array|min:1',
            'milestones.*.milestone_id' => 'required|exists:milestones,id',
            'milestones.*.target_dt' => 'required|date',
            'milestones.*.completed_dt' => 'nullable|date',
            'clusters' => 'required|array|min:1',
            'clusters.*.name' => 'required|string|max:255',
            'clusters.*.desc' => 'nullable|string',
            'clusters.*.facilities' => 'nullable|string',
            'clusters.*.road_width' => 'required|numeric|min:0',
            'clusters.*.products' => 'required|array|min:1',
            'clusters.*.products.*.product_id' => 'required|exists:products,id',
            'clusters.*.products.*.qty' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            // Create project
            $project = Project::create([
                'name' => $request->name,
                'land_id' => $request->land_id,
                'dt_start' => $request->dt_start,
                'dt_end' => $request->dt_end,
                'status' => $request->status,
            ]);

            // Attach contractors
            foreach ($request->contractors as $contractorId) {
                ProjectContractor::create([
                    'project_id' => $project->id,
                    'contractor_id' => $contractorId,
                ]);
            }

            // Create project milestones
            foreach ($request->milestones as $milestoneData) {
                ProjectMilestone::create([
                    'project_id' => $project->id,
                    'milestone_id' => $milestoneData['milestone_id'],
                    'target_dt' => $milestoneData['target_dt'],
                    'completed_dt' => $milestoneData['completed_dt'] ?? null,
                    'status' => $milestoneData['completed_dt'] ? 'completed' : 'pending',
                ]);
            }

            // Collect all materials needed for purchase orders
            $materialsNeeded = [];

            // Create clusters and units
            foreach ($request->clusters as $clusterData) {
                $cluster = Cluster::create([
                    'project_id' => $project->id,
                    'name' => $clusterData['name'],
                    'desc' => $clusterData['desc'] ?? null,
                    'facilities' => $clusterData['facilities'] ?? null,
                    'road_width' => $clusterData['road_width'],
                ]);

                // Create units based on products and quantities
                foreach ($clusterData['products'] as $productData) {
                    $product = Product::find($productData['product_id']);
                    $quantity = (int) $productData['qty'];

                    // Create units equal to quantity
                    for ($i = 1; $i <= $quantity; $i++) {
                        // Generate unit number (sequential per project)
                        $unitNumber = $this->generateUnitNumber($project->id);

                        Unit::create([
                            'name' => $cluster->name . ' - Unit ' . $unitNumber,
                            'no' => $unitNumber,
                            'price' => $product->price,
                            'product_id' => $product->id,
                            'cluster_id' => $cluster->id,
                            'sales_id' => 1, // Default sales_id, adjust as needed
                            'desc' => $product->type->name ?? 'Unit',
                            'facilities' => $cluster->facilities,
                            'status' => 'available',
                        ]);
                    }

                    // Collect materials from product formula
                    if ($product->formula) {
                        foreach ($product->formula->details as $detail) {
                            $materialId = $detail->material_id;
                            $qtyNeeded = $detail->qty * $quantity;

                            if (isset($materialsNeeded[$materialId])) {
                                $materialsNeeded[$materialId] += $qtyNeeded;
                            } else {
                                $materialsNeeded[$materialId] = $qtyNeeded;
                            }
                        }
                    }
                }
            }

            // Auto-generate purchase orders based on materials needed
            $this->generatePurchaseOrders($project->id, $materialsNeeded);

            DB::commit();

            return redirect()->route('projects.index')
                ->with('success', 'Project created successfully with clusters, units, and purchase orders generated!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Failed to create project: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified project
     */
    public function show(Project $project)
    {
        $project->load(['land', 'contractors', 'projectMilestones.milestone', 'clusters.units.product', 'purchaseOrders']);
        return view('projects.show', compact('project'));
    }

    /**
     * Show the form for editing the specified project
     */
    public function edit(Project $project)
    {
        $project->load(['contractors', 'projectMilestones', 'clusters.units']);
        $lands = Land::all();
        $contractors = Contractor::all();
        $milestones = Milestone::all();
        $products = Product::with('type')->get();

        return view('projects.edit', compact('project', 'lands', 'contractors', 'milestones', 'products'));
    }

    /**
     * Update the specified project in storage
     */
    public function update(Request $request, Project $project)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'land_id' => 'required|exists:lands,id',
            'dt_start' => 'required|date',
            'dt_end' => 'required|date|after_or_equal:dt_start',
            'status' => 'required|in:pending,in_progress,completed',
            'contractors' => 'required|array|min:1',
            'contractors.*' => 'required|exists:contractors,id',
            'milestones' => 'required|array|min:1',
            'milestones.*.milestone_id' => 'required|exists:milestones,id',
            'milestones.*.target_dt' => 'required|date',
            'milestones.*.completed_dt' => 'nullable|date',
            'clusters' => 'required|array|min:1',
            'clusters.*.name' => 'required|string|max:255',
            'clusters.*.desc' => 'nullable|string',
            'clusters.*.facilities' => 'nullable|string',
            'clusters.*.road_width' => 'required|numeric|min:0',
            'clusters.*.products' => 'required|array|min:1',
            'clusters.*.products.*.product_id' => 'required|exists:products,id',
            'clusters.*.products.*.qty' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            // Update project basic info
            $project->update([
                'name' => $request->name,
                'land_id' => $request->land_id,
                'dt_start' => $request->dt_start,
                'dt_end' => $request->dt_end,
                'status' => $request->status,
            ]);

            // Update contractors (delete old, create new)
            ProjectContractor::where('project_id', $project->id)->delete();
            foreach ($request->contractors as $contractorId) {
                ProjectContractor::create([
                    'project_id' => $project->id,
                    'contractor_id' => $contractorId,
                ]);
            }

            // Update project milestones (delete old, create new)
            ProjectMilestone::where('project_id', $project->id)->delete();
            foreach ($request->milestones as $milestoneData) {
                ProjectMilestone::create([
                    'project_id' => $project->id,
                    'milestone_id' => $milestoneData['milestone_id'],
                    'target_dt' => $milestoneData['target_dt'],
                    'completed_dt' => $milestoneData['completed_dt'] ?? null,
                    'status' => $milestoneData['completed_dt'] ? 'completed' : 'pending',
                ]);
            }

            // Delete old units and clusters
            Unit::whereIn('cluster_id', $project->clusters->pluck('id'))->delete();
            Cluster::where('project_id', $project->id)->delete();

            // Collect materials for new purchase orders
            $materialsNeeded = [];

            // Create new clusters and units
            foreach ($request->clusters as $clusterData) {
                $cluster = Cluster::create([
                    'project_id' => $project->id,
                    'name' => $clusterData['name'],
                    'desc' => $clusterData['desc'] ?? null,
                    'facilities' => $clusterData['facilities'] ?? null,
                    'road_width' => $clusterData['road_width'],
                ]);

                foreach ($clusterData['products'] as $productData) {
                    $product = Product::find($productData['product_id']);
                    $quantity = (int) $productData['qty'];

                    for ($i = 1; $i <= $quantity; $i++) {
                        $unitNumber = $this->generateUnitNumber($project->id);

                        Unit::create([
                            'name' => $cluster->name . ' - Unit ' . $unitNumber,
                            'no' => $unitNumber,
                            'price' => $product->price,
                            'product_id' => $product->id,
                            'cluster_id' => $cluster->id,
                            'sales_id' => 1,
                            'desc' => $product->type->name ?? 'Unit',
                            'facilities' => $cluster->facilities,
                            'status' => 'available',
                        ]);
                    }

                    if ($product->formula) {
                        foreach ($product->formula->details as $detail) {
                            $materialId = $detail->material_id;
                            $qtyNeeded = $detail->qty * $quantity;

                            if (isset($materialsNeeded[$materialId])) {
                                $materialsNeeded[$materialId] += $qtyNeeded;
                            } else {
                                $materialsNeeded[$materialId] = $qtyNeeded;
                            }
                        }
                    }
                }
            }

            // Generate new purchase orders if materials changed
            if (!empty($materialsNeeded)) {
                $this->generatePurchaseOrders($project->id, $materialsNeeded);
            }

            DB::commit();

            return redirect()->route('projects.index')
                ->with('success', 'Project updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Failed to update project: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified project from storage
     */
    public function destroy(Project $project)
    {
        try {
            DB::beginTransaction();

            // Delete related data
            Unit::whereIn('cluster_id', $project->clusters->pluck('id'))->delete();
            Cluster::where('project_id', $project->id)->delete();
            ProjectContractor::where('project_id', $project->id)->delete();
            ProjectMilestone::where('project_id', $project->id)->delete();

            // Don't delete purchase orders, just disconnect them
            PurchaseOrder::where('project_id', $project->id)->update(['project_id' => null]);

            $project->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Project deleted successfully!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete project: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export projects to Excel
     */
    public function export(Request $request)
    {
        return Excel::download(
            new ProjectsExport($request->status, $request->start_date, $request->end_date),
            'projects_' . now()->format('Y-m-d_His') . '.xlsx'
        );
    }

    /**
     * Generate unit number (sequential per project, reset per project)
     */
    private function generateUnitNumber($projectId)
    {
        $lastUnit = Unit::whereHas('cluster', function ($query) use ($projectId) {
            $query->where('project_id', $projectId);
        })->orderBy('id', 'desc')->first();

        if ($lastUnit && $lastUnit->no) {
            // Extract number and increment
            $lastNumber = (int) preg_replace('/[^0-9]/', '', $lastUnit->no);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Generate purchase orders based on materials needed
     */
    private function generatePurchaseOrders($projectId, $materialsNeeded)
    {
        if (empty($materialsNeeded)) {
            return;
        }

        // Group materials by supplier
        $materialsBySupplier = [];
        foreach ($materialsNeeded as $materialId => $qty) {
            $material = Material::find($materialId);
            if ($material && $material->supplier_id) {
                $supplierId = $material->supplier_id;
                if (!isset($materialsBySupplier[$supplierId])) {
                    $materialsBySupplier[$supplierId] = [];
                }
                $materialsBySupplier[$supplierId][$materialId] = $qty;
            }
        }

        // Create purchase orders per supplier
        foreach ($materialsBySupplier as $supplierId => $materials) {
            $total = 0;

            // Generate PO number
            $lastPO = PurchaseOrder::orderBy('id', 'desc')->first();
            $poNumber = $lastPO ? 'PO-' . str_pad(((int) substr($lastPO->no, 3)) + 1, 6, '0', STR_PAD_LEFT) : 'PO-000001';

            // Calculate total
            foreach ($materials as $materialId => $qty) {
                $material = Material::find($materialId);
                $total += ($material->price * $qty);
            }

            // Create purchase order
            $purchaseOrder = PurchaseOrder::create([
                'no' => $poNumber,
                'dt' => now(),
                'project_id' => $projectId,
                'supplier_id' => $supplierId,
                'total' => $total,
                'status' => 'pending',
            ]);

            // Create purchase order details
            foreach ($materials as $materialId => $qty) {
                PurchaseOrderDetail::create([
                    'purchase_order_id' => $purchaseOrder->id,
                    'material_id' => $materialId,
                    'qty' => $qty,
                ]);

                // Increment material stock
                $material = Material::find($materialId);
                $material->increment('qty', $qty);
            }
        }
    }
}
