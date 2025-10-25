<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Exports\SalesExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class SaleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Sale::query();

            // Apply date range filter if provided
            if ($request->filled('start_date') && $request->filled('end_date')) {
                $query->whereBetween('created_at', [
                    $request->start_date . ' 00:00:00',
                    $request->end_date . ' 23:59:59'
                ]);
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('action', function ($sale) {
                    return view('sales.partials.actions', compact('sale'))->render();
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('sales.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('sales.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:255',
        ]);

        try {
            Sale::create($validated);

            return redirect()
                ->route('sales.index')
                ->with('success', 'Sale created successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to create sale: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Sale $sale)
    {
        return view('sales.show', compact('sale'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Sale $sale)
    {
        return view('sales.edit', compact('sale'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Sale $sale)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:255',
        ]);

        try {
            $sale->update($validated);

            return redirect()
                ->route('sales.index')
                ->with('success', 'Sale updated successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to update sale: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Sale $sale)
    {
        try {
            $sale->delete();

            return response()->json([
                'success' => true,
                'message' => 'Sale deleted successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete sale: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export sales to Excel.
     */
    public function export(Request $request)
    {
        try {
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');
            
            $fileName = 'sales_' . date('YmdHis') . '.xlsx';
            
            return Excel::download(
                new SalesExport($startDate, $endDate), 
                $fileName
            );
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Failed to export sales: ' . $e->getMessage());
        }
    }
}
