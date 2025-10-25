<?php

namespace App\Http\Controllers;

use App\Models\Milestone;
use App\Exports\MilestonesExport;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class MilestoneController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Milestone::query();

            // Apply date range filter if provided
            if ($request->filled('start_date') && $request->filled('end_date')) {
                $query->whereBetween('created_at', [
                    $request->start_date . ' 00:00:00',
                    $request->end_date . ' 23:59:59'
                ]);
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->editColumn('desc', function ($milestone) {
                    return $milestone->desc ? Str::limit($milestone->desc, 100) : '-';
                })
                ->addColumn('action', function ($milestone) {
                    return view('milestones.partials.actions', compact('milestone'))->render();
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('milestones.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('milestones.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'desc' => 'nullable|string',
        ]);

        try {
            Milestone::create($validated);

            return redirect()
                ->route('milestones.index')
                ->with('success', 'Milestone created successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to create milestone: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Milestone $milestone)
    {
        return view('milestones.show', compact('milestone'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Milestone $milestone)
    {
        return view('milestones.edit', compact('milestone'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Milestone $milestone)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'desc' => 'nullable|string',
        ]);

        try {
            $milestone->update($validated);

            return redirect()
                ->route('milestones.index')
                ->with('success', 'Milestone updated successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to update milestone: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Milestone $milestone)
    {
        try {
            $milestone->delete();

            return response()->json([
                'success' => true,
                'message' => 'Milestone deleted successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete milestone: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export milestones to Excel.
     */
    public function export(Request $request)
    {
        try {
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');
            
            $fileName = 'milestones_' . date('YmdHis') . '.xlsx';
            
            return Excel::download(
                new MilestonesExport($startDate, $endDate), 
                $fileName
            );
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Failed to export milestones: ' . $e->getMessage());
        }
    }
}
