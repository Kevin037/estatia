<?php

namespace App\Http\Controllers;

use App\Models\Contractor;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Exports\ContractorsExport;
use Maatwebsite\Excel\Facades\Excel;

class ContractorController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Contractor::query();

            if ($request->filled('start_date') && $request->filled('end_date')) {
                $query->whereBetween('created_at', [
                    $request->start_date . ' 00:00:00',
                    $request->end_date . ' 23:59:59'
                ]);
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('action', function ($contractor) {
                    return view('contractors.partials.actions', compact('contractor'));
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('contractors.index');
    }

    public function create()
    {
        return view('contractors.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
        ]);

        Contractor::create($validated);

        return redirect()->route('contractors.index')
            ->with('success', 'Contractor created successfully.');
    }

    public function edit(Contractor $contractor)
    {
        return view('contractors.edit', compact('contractor'));
    }

    public function update(Request $request, Contractor $contractor)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
        ]);

        $contractor->update($validated);

        return redirect()->route('contractors.index')
            ->with('success', 'Contractor updated successfully.');
    }

    public function destroy(Contractor $contractor)
    {
        $contractor->delete();

        return response()->json([
            'success' => true,
            'message' => 'Contractor deleted successfully.'
        ]);
    }

    public function export(Request $request)
    {
        return Excel::download(
            new ContractorsExport($request->start_date, $request->end_date),
            'contractors_' . date('Y-m-d_His') . '.xlsx'
        );
    }
}

