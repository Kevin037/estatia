<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductPhoto;
use App\Models\Formula;
use App\Exports\ProductExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
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
                ->addColumn('photos_count', function ($product) {
                    $count = $product->productPhotos()->count();
                    if ($count > 0) {
                        return '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">' . $count . ' photos</span>';
                    }
                    return '<span class="text-gray-400 text-xs">No photos</span>';
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
                ->rawColumns(['photo', 'photos_count', 'price', 'qty', 'action'])
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
            'product_photos' => ['nullable', 'array'],
            'product_photos.*' => ['image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ]);

        DB::beginTransaction();
        try {
            // Handle main photo upload
            if ($request->hasFile('photo')) {
                $validated['photo'] = $request->file('photo')->store('products', 'public');
            }

            // Create product
            $product = Product::create($validated);

            // Handle multiple product photos
            if ($request->hasFile('product_photos')) {
                foreach ($request->file('product_photos') as $index => $photoFile) {
                    $photoPath = $photoFile->store('products/photos', 'public');
                    
                    ProductPhoto::create([
                        'product_id' => $product->id,
                        'name' => 'Photo ' . ($index + 1),
                        'photo' => $photoPath,
                    ]);
                }
            }

            DB::commit();

            return redirect()
                ->route('products.index')
                ->with('success', 'Product created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
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
        $product->load('productPhotos');
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
            'product_photos' => ['nullable', 'array'],
            'product_photos.*' => ['image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'delete_photos' => ['nullable', 'array'],
            'delete_photos.*' => ['exists:product_photos,id'],
        ]);

        DB::beginTransaction();
        try {
            // Handle main photo upload
            if ($request->hasFile('photo')) {
                // Delete old photo if exists
                if ($product->photo) {
                    Storage::disk('public')->delete($product->photo);
                }
                $validated['photo'] = $request->file('photo')->store('products', 'public');
            }

            $product->update($validated);

            // Handle deleting product photos
            if ($request->filled('delete_photos')) {
                $photosToDelete = ProductPhoto::whereIn('id', $request->delete_photos)
                    ->where('product_id', $product->id)
                    ->get();
                
                foreach ($photosToDelete as $photo) {
                    Storage::disk('public')->delete($photo->photo);
                    $photo->delete();
                }
            }

            // Handle new product photos
            if ($request->hasFile('product_photos')) {
                foreach ($request->file('product_photos') as $index => $photoFile) {
                    $photoPath = $photoFile->store('products/photos', 'public');
                    
                    ProductPhoto::create([
                        'product_id' => $product->id,
                        'name' => 'Photo ' . ($product->productPhotos()->count() + $index + 1),
                        'photo' => $photoPath,
                    ]);
                }
            }

            DB::commit();

            return redirect()
                ->route('products.index')
                ->with('success', 'Product updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
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
        DB::beginTransaction();
        try {
            // Delete main photo if exists
            if ($product->photo) {
                Storage::disk('public')->delete($product->photo);
            }

            // Delete all product photos
            foreach ($product->productPhotos as $photo) {
                Storage::disk('public')->delete($photo->photo);
                $photo->delete();
            }

            $product->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Product deleted successfully.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
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
