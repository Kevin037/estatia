<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Formula;
use App\Exports\ProductExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Product::with('formula');

            // Apply date range filter if provided
            if ($request->filled('start_date') && $request->filled('end_date')) {
                $query->whereBetween('created_at', [
                    $request->start_date . ' 00:00:00',
                    $request->end_date . ' 23:59:59'
                ]);
            }

            // Apply search filters
            if ($request->filled('search_name')) {
                $query->where('name', 'like', '%' . $request->search_name . '%');
            }

            if ($request->filled('search_sku')) {
                $query->where('sku', 'like', '%' . $request->search_sku . '%');
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('photo', function ($product) {
                    if ($product->photo) {
                        return '<img src="' . $product->photo_url . '" alt="' . $product->name . '" class="h-12 w-12 rounded object-cover">';
                    }
                    return '<div class="h-12 w-12 rounded bg-gray-200 flex items-center justify-center text-gray-400 text-xs">No Image</div>';
                })
                ->editColumn('price', function ($product) {
                    return '<span class="font-medium text-emerald-600">Rp ' . number_format($product->price, 0, ',', '.') . '</span>';
                })
                ->editColumn('qty', function ($product) {
                    return '<span class="font-medium">' . number_format($product->qty, 2) . '</span>';
                })
                ->addColumn('action', function ($product) {
                    return view('products.partials.actions', compact('product'))->render();
                })
                ->rawColumns(['photo', 'price', 'qty', 'action'])
                ->make(true);
        }

        return view('products.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $formulas = Formula::orderBy('name')->get();
        return view('products.create', compact('formulas'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'sku' => ['required', 'string', 'max:255', 'unique:products,sku'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'price' => ['required', 'numeric', 'min:0'],
            'formula_id' => ['nullable', 'exists:formulas,id'],
        ]);

        try {
            // Handle photo upload
            if ($request->hasFile('photo')) {
                $validated['photo'] = $request->file('photo')->store('products', 'public');
            }

            // Qty defaults to 0 in migration
            Product::create($validated);

            return redirect()
                ->route('products.index')
                ->with('success', 'Product created successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to create product: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        $product->load('formula');
        return view('products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        $formulas = Formula::orderBy('name')->get();
        return view('products.edit', compact('product', 'formulas'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'sku' => ['required', 'string', 'max:255', 'unique:products,sku,' . $product->id],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'price' => ['required', 'numeric', 'min:0'],
            'formula_id' => ['nullable', 'exists:formulas,id'],
        ]);

        try {
            // Handle photo upload
            if ($request->hasFile('photo')) {
                // Delete old photo if exists
                if ($product->photo) {
                    Storage::disk('public')->delete($product->photo);
                }
                $validated['photo'] = $request->file('photo')->store('products', 'public');
            }

            $product->update($validated);

            return redirect()
                ->route('products.index')
                ->with('success', 'Product updated successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to update product: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        try {
            // Delete photo if exists
            if ($product->photo) {
                Storage::disk('public')->delete($product->photo);
            }

            $product->delete();

            return response()->json([
                'success' => true,
                'message' => 'Product deleted successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete product: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export products to Excel.
     */
    public function export(Request $request)
    {
        try {
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');
            
            $fileName = 'products_' . date('YmdHis') . '.xlsx';
            
            return Excel::download(
                new ProductExport($startDate, $endDate), 
                $fileName
            );
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Failed to export products: ' . $e->getMessage());
        }
    }
}
