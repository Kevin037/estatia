<?php

namespace App\Http\Controllers;

use App\Models\Land;
use App\Exports\LandsExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class LandController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Land::query();

            // Apply date range filter if provided
            if ($request->filled('start_date') && $request->filled('end_date')) {
                $query->whereBetween('created_at', [
                    $request->start_date . ' 00:00:00',
                    $request->end_date . ' 23:59:59'
                ]);
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->editColumn('length', function ($land) {
                    return number_format($land->length, 2) . ' m';
                })
                ->editColumn('wide', function ($land) {
                    return number_format($land->wide, 2) . ' m';
                })
                ->addColumn('photo', function ($land) {
                    if ($land->photo) {
                        return '<img src="' . asset('storage/' . $land->photo) . '" alt="' . $land->name . '" class="h-10 w-10 rounded object-cover">';
                    }
                    return '<div class="h-10 w-10 rounded bg-gray-100 flex items-center justify-center text-gray-400 text-xs">No Photo</div>';
                })
                ->addColumn('action', function ($land) {
                    return view('lands.partials.actions', compact('land'))->render();
                })
                ->rawColumns(['photo', 'action'])
                ->make(true);
        }

        return view('lands.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('lands.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'wide' => 'required|numeric|min:0',
            'length' => 'required|numeric|min:0',
            'location' => 'nullable|string',
            'desc' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
        ]);

        try {
            // Handle photo upload
            if ($request->hasFile('photo')) {
                $validated['photo'] = $request->file('photo')->store('lands', 'public');
            }
            
            Land::create($validated);

            return redirect()
                ->route('lands.index')
                ->with('success', 'Land created successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to create land: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Land $land)
    {
        return view('lands.show', compact('land'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Land $land)
    {
        return view('lands.edit', compact('land'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Land $land)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'wide' => 'required|numeric|min:0',
            'length' => 'required|numeric|min:0',
            'location' => 'nullable|string',
            'desc' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
        ]);

        try {
            // Handle photo upload
            if ($request->hasFile('photo')) {
                // Delete old photo if exists
                if ($land->photo) {
                    Storage::disk('public')->delete($land->photo);
                }
                $validated['photo'] = $request->file('photo')->store('lands', 'public');
            }
            
            $land->update($validated);

            return redirect()
                ->route('lands.index')
                ->with('success', 'Land updated successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to update land: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Land $land)
    {
        try {
            // Delete photo if exists
            if ($land->photo) {
                Storage::disk('public')->delete($land->photo);
            }
            
            $land->delete();

            return response()->json([
                'success' => true,
                'message' => 'Land deleted successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete land: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export lands to Excel.
     */
    public function export(Request $request)
    {
        try {
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');
            
            $fileName = 'lands_' . date('YmdHis') . '.xlsx';
            
            return Excel::download(
                new LandsExport($startDate, $endDate), 
                $fileName
            );
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Failed to export lands: ' . $e->getMessage());
        }
    }
}
