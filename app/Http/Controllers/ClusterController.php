<?php

namespace App\Http\Controllers;

use App\Models\Cluster;
use App\Models\Project;
use App\Models\Unit;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ClusterController extends Controller
{
    /**
     * Display a listing of clusters with DataTables
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Cluster::with(['project', 'units']);

            // Project filter
            if ($request->filled('project_id')) {
                $query->where('project_id', $request->project_id);
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('project_name', function ($cluster) {
                    return $cluster->project->name ?? '-';
                })
                ->addColumn('total_units', function ($cluster) {
                    $count = $cluster->units()->count();
                    return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ' . 
                           ($count > 0 ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-800') . '">' . 
                           $count . ' units</span>';
                })
                ->addColumn('price_range', function ($cluster) {
                    $units = $cluster->units;
                    
                    if ($units->isEmpty()) {
                        return '<span class="text-gray-400">-</span>';
                    }
                    
                    $minPrice = $units->min('price');
                    $maxPrice = $units->max('price');
                    
                    if ($minPrice == $maxPrice) {
                        return '<span class="text-sm font-medium text-gray-900">Rp ' . number_format($minPrice, 0, ',', '.') . '</span>';
                    }
                    
                    return '<div class="text-sm">
                        <div class="font-medium text-gray-900">Rp ' . number_format($minPrice, 0, ',', '.') . '</div>
                        <div class="text-gray-500">to Rp ' . number_format($maxPrice, 0, ',', '.') . '</div>
                    </div>';
                })
                ->addColumn('status', function ($cluster) {
                    $totalUnits = $cluster->units()->count();
                    $availableUnits = $cluster->units()->where('status', 'available')->count();
                    $soldUnits = $cluster->units()->whereIn('status', ['sold', 'handed_over'])->count();
                    
                    if ($totalUnits == 0) {
                        return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">No Units</span>';
                    }
                    
                    if ($soldUnits == $totalUnits) {
                        return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Sold Out</span>';
                    }
                    
                    if ($availableUnits == $totalUnits) {
                        return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">All Available</span>';
                    }
                    
                    return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Partially Sold</span>';
                })
                ->addColumn('action', function ($cluster) {
                    return view('clusters.actions', compact('cluster'));
                })
                ->rawColumns(['total_units', 'price_range', 'status', 'action'])
                ->make(true);
        }

        // Get all projects for filter dropdown
        $projects = Project::orderBy('name')->get();

        return view('clusters.index', compact('projects'));
    }

    /**
     * Display the specified cluster with units
     */
    public function show(Request $request, Cluster $cluster)
    {
        // Eager load relationships
        $cluster->load(['project', 'units.product.type', 'units.sales', 'units.unitPhotos']);

        // If Ajax request for units datatable
        if ($request->ajax()) {
            $query = Unit::with(['product.type', 'sales', 'unitPhotos'])
                ->where('cluster_id', $cluster->id);

            // Apply filters from units index
            // Type filter
            if ($request->filled('type_id')) {
                $query->whereHas('product', function($q) use ($request) {
                    $q->where('type_id', $request->type_id);
                });
            }

            // Status filter
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            // Price range filters
            if ($request->filled('min_price')) {
                $query->where('price', '>=', $request->min_price);
            }

            if ($request->filled('max_price')) {
                $query->where('price', '<=', $request->max_price);
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('type_name', function ($unit) {
                    return $unit->product->type->name ?? '-';
                })
                ->addColumn('price', function ($unit) {
                    return '<span class="text-sm font-semibold text-emerald-600">Rp ' . number_format($unit->price, 0, ',', '.') . '</span>';
                })
                ->addColumn('photos_count', function ($unit) {
                    $count = $unit->unitPhotos()->count();
                    return $count > 0 
                        ? '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">' . $count . ' photos</span>'
                        : '<span class="text-gray-400 text-xs">No photos</span>';
                })
                ->addColumn('status', function ($unit) {
                    $colors = [
                        'available' => 'bg-green-100 text-green-800',
                        'reserved' => 'bg-yellow-100 text-yellow-800',
                        'sold' => 'bg-blue-100 text-blue-800',
                        'handed_over' => 'bg-gray-100 text-gray-800',
                    ];
                    $color = $colors[$unit->status] ?? 'bg-gray-100 text-gray-800';
                    return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ' . $color . '">' . 
                           ucfirst(str_replace('_', ' ', $unit->status)) . '</span>';
                })
                ->addColumn('action', function ($unit) {
                    return '
                        <div class="flex items-center space-x-2">
                            <a href="' . route('units.show', $unit) . '" class="inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <svg class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                View
                            </a>
                            <a href="' . route('units.edit', $unit) . '" class="inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">
                                <svg class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                </svg>
                                Edit
                            </a>
                        </div>
                    ';
                })
                ->rawColumns(['price', 'photos_count', 'status', 'action'])
                ->make(true);
        }

        // Get types for filter dropdown
        $types = \App\Models\Type::orderBy('name')->get();

        return view('clusters.show', compact('cluster', 'types'));
    }
}
