<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use App\Exports\FeedbacksExport;
use Maatwebsite\Excel\Facades\Excel;

class FeedbackController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $feedbacks = Feedback::with(['order.customer'])
                ->select('feedbacks.*');

            // Apply date filter if provided
            if ($request->filled('start_date') && $request->filled('end_date')) {
                $feedbacks->dateRange($request->start_date, $request->end_date);
            }

            return DataTables::of($feedbacks)
                ->addIndexColumn()
                ->addColumn('date', function ($feedback) {
                    return $feedback->dt ? $feedback->dt->format('d M Y') : 'N/A';
                })
                ->addColumn('order_no', function ($feedback) {
                    $orderNo = $feedback->order->no ?? 'N/A';
                    $customer = $feedback->order->customer->name ?? 'Unknown';
                    return '<div class="font-medium text-gray-900">' . e($orderNo) . '</div>
                            <div class="text-sm text-gray-500">' . e($customer) . '</div>';
                })
                ->addColumn('action', function ($feedback) {
                    return view('feedbacks.partials.actions', compact('feedback'))->render();
                })
                ->rawColumns(['order_no', 'action'])
                ->make(true);
        }

        return view('feedbacks.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $orders = Order::with('customer')->get();

        return view('feedbacks.create', compact('orders'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'dt' => 'required|date',
            'desc' => 'required|string',
            'photo' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
        ]);

        // Handle photo upload
        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('feedbacks', 'public');
        }

        Feedback::create($validated);

        return redirect()->route('feedbacks.index')
            ->with('success', 'Feedback created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Feedback $feedback)
    {
        $feedback->load([
            'order.customer',
            'order.project',
            'order.cluster',
            'order.unit.product'
        ]);

        return view('feedbacks.show', compact('feedback'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Feedback $feedback)
    {
        $orders = Order::with('customer')
            ->where('status', 'completed')
            ->orWhere('id', $feedback->order_id)
            ->get();

        return view('feedbacks.edit', compact('feedback', 'orders'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Feedback $feedback)
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'dt' => 'required|date',
            'desc' => 'required|string',
            'photo' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
        ]);

        // Handle photo upload
        if ($request->hasFile('photo')) {
            // Delete old photo
            if ($feedback->photo && Storage::disk('public')->exists($feedback->photo)) {
                Storage::disk('public')->delete($feedback->photo);
            }
            $validated['photo'] = $request->file('photo')->store('feedbacks', 'public');
        }

        $feedback->update($validated);

        return redirect()->route('feedbacks.index')
            ->with('success', 'Feedback updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Feedback $feedback)
    {
        // Delete photo if exists
        if ($feedback->photo && Storage::disk('public')->exists($feedback->photo)) {
            Storage::disk('public')->delete($feedback->photo);
        }

        $feedback->delete();

        return response()->json([
            'success' => true,
            'message' => 'Feedback deleted successfully!'
        ]);
    }

    /**
     * Export feedbacks to Excel
     */
    public function export(Request $request)
    {
        return Excel::download(
            new FeedbacksExport($request->start_date, $request->end_date),
            'feedbacks_' . now()->format('Y-m-d_His') . '.xlsx'
        );
    }
}
