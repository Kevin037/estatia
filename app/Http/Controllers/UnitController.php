<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\UnitPhoto;
use App\Models\Project;
use App\Models\Cluster;
use App\Models\Product;
use App\Models\Type;
use App\Models\Sales;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class UnitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Unit::with(['product.type', 'cluster.project', 'sales', 'unitPhotos']);

            // Apply filters
            if ($request->filled('project_id')) {
                $query->whereHas('cluster', function($q) use ($request) {
                    $q->where('project_id', $request->project_id);
                });
            }

            if ($request->filled('type_id')) {
                $query->whereHas('product', function($q) use ($request) {
                    $q->where('type_id', $request->type_id);
                });
            }

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('min_price')) {
                $query->where('price', '>=', $request->min_price);
            }

            if ($request->filled('max_price')) {
                $query->where('price', '<=', $request->max_price);
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('project_name', function ($unit) {
                    return $unit->cluster->project->name ?? '-';
                })
                ->addColumn('cluster_name', function ($unit) {
                    return $unit->cluster->name ?? '-';
                })
                ->addColumn('type_name', function ($unit) {
                    return $unit->product->type->name ?? '-';
                })
                ->addColumn('photos_count', function ($unit) {
                    $count = $unit->unitPhotos()->count();
                    if ($count > 0) {
                        return '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">' . $count . ' photos</span>';
                    }
                    return '<span class="text-gray-400 text-xs">No photos</span>';
                })
                ->addColumn('price', function ($unit) {
                    return '<span class="font-medium text-gray-900">Rp ' . number_format($unit->price, 0, ',', '.') . '</span>';
                })
                ->addColumn('status', function ($unit) {
                    $colors = [
                        'available' => 'bg-green-100 text-green-800',
                        'reserved' => 'bg-yellow-100 text-yellow-800',
                        'sold' => 'bg-blue-100 text-blue-800',
                        'handed_over' => 'bg-gray-100 text-gray-800',
                    ];
                    $color = $colors[$unit->status] ?? 'bg-gray-100 text-gray-800';
                    return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ' . $color . '">' . ucfirst(str_replace('_', ' ', $unit->status)) . '</span>';
                })
                ->addColumn('action', function ($unit) {
                    return view('units.actions', compact('unit'))->render();
                })
                ->rawColumns(['photos_count', 'price', 'status', 'action'])
                ->make(true);
        }

        $projects = Project::orderBy('name')->get();
        $types = Type::orderBy('name')->get();

        return view('units.index', compact('projects', 'types'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Unit $unit)
    {
        $unit->load(['product.type', 'product.formula', 'product.productPhotos', 'cluster.project', 'sales', 'unitPhotos']);
        
        return view('units.show', compact('unit'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Unit $unit)
    {
        $unit->load(['product.type', 'cluster.project', 'sales', 'unitPhotos']);
        
        $projects = Project::orderBy('name')->get();
        $clusters = Cluster::orderBy('name')->get();
        $products = Product::with('type')->orderBy('name')->get();
        $salesList = Sales::orderBy('name')->get();

        return view('units.edit', compact('unit', 'projects', 'clusters', 'products', 'salesList'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Unit $unit)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'no' => ['nullable', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'product_id' => ['required', 'exists:products,id'],
            'cluster_id' => ['required', 'exists:clusters,id'],
            'sales_id' => ['nullable', 'exists:sales,id'],
            'desc' => ['nullable', 'string'],
            'facilities' => ['nullable', 'string'],
            'status' => ['required', 'in:available,reserved,sold,handed_over'],
            'unit_photos' => ['nullable', 'array'],
            'unit_photos.*' => ['image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'delete_photos' => ['nullable', 'array'],
            'delete_photos.*' => ['exists:unit_photos,id'],
        ]);

        DB::beginTransaction();
        try {
            // Update unit data
            $unit->update([
                'name' => $request->name,
                'no' => $request->no,
                'price' => $request->price,
                'product_id' => $request->product_id,
                'cluster_id' => $request->cluster_id,
                'sales_id' => $request->sales_id,
                'desc' => $request->desc,
                'facilities' => $request->facilities,
                'status' => $request->status,
            ]);

            // Delete selected photos
            if ($request->filled('delete_photos')) {
                foreach ($request->delete_photos as $photoId) {
                    $photo = UnitPhoto::find($photoId);
                    if ($photo && $photo->unit_id == $unit->id) {
                        Storage::disk('public')->delete($photo->photo);
                        $photo->delete();
                    }
                }
            }

            // Upload new photos
            if ($request->hasFile('unit_photos')) {
                foreach ($request->file('unit_photos') as $index => $photoFile) {
                    $photoPath = $photoFile->store('units/photos', 'public');
                    UnitPhoto::create([
                        'unit_id' => $unit->id,
                        'name' => 'Photo ' . ($unit->unitPhotos()->count() + $index + 1),
                        'photo' => $photoPath,
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('units.show', $unit)
                ->with('success', 'Unit updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update unit: ' . $e->getMessage());
        }
    }
}
