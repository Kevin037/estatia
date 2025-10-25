<?php

namespace App\Http\Controllers;

use App\Models\Type;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Exports\TypesExport;
use Maatwebsite\Excel\Facades\Excel;

class TypeController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Type::query();

            if ($request->filled('start_date') && $request->filled('end_date')) {
                $query->whereBetween('created_at', [
                    $request->start_date . ' 00:00:00',
                    $request->end_date . ' 23:59:59'
                ]);
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->editColumn('land_area', function ($type) {
                    return number_format($type->land_area, 2) . ' m²';
                })
                ->editColumn('building_area', function ($type) {
                    return number_format($type->building_area, 2) . ' m²';
                })
                ->addColumn('action', function ($type) {
                    return view('types.partials.actions', compact('type'));
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('types.index');
    }

    public function create()
    {
        return view('types.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'land_area' => 'required|numeric|min:0',
            'building_area' => 'required|numeric|min:0',
        ]);

        Type::create($validated);

        return redirect()->route('types.index')
            ->with('success', 'Type created successfully.');
    }

    public function edit(Type $type)
    {
        return view('types.edit', compact('type'));
    }

    public function update(Request $request, Type $type)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'land_area' => 'required|numeric|min:0',
            'building_area' => 'required|numeric|min:0',
        ]);

        $type->update($validated);

        return redirect()->route('types.index')
            ->with('success', 'Type updated successfully.');
    }

    public function destroy(Type $type)
    {
        $type->delete();

        return response()->json([
            'success' => true,
            'message' => 'Type deleted successfully.'
        ]);
    }

    public function export(Request $request)
    {
        return Excel::download(
            new TypesExport($request->start_date, $request->end_date),
            'types_' . date('Y-m-d_His') . '.xlsx'
        );
    }
}

