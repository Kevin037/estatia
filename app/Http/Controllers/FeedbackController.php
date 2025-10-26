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
                ->addColumn('order_no', function ($feedback) {
                    $phoneNumber = preg_replace('/[^0-9]/', '', $feedback->order->customer->phone);
                    // Add country code if not present (assuming Indonesia +62)
                    if (!str_starts_with($phoneNumber, '62')) {
                        if (str_starts_with($phoneNumber, '0')) {
                            $phoneNumber = '62' . substr($phoneNumber, 1);
                        } else {
                            $phoneNumber = '62' . $phoneNumber;
                        }
                    }
                    $whatsappUrl = 'https://wa.me/' . $phoneNumber;
                    
                    $orderNo = $feedback->order->no ?? 'N/A';
                    $customer = $feedback->order->customer->name ?? 'Unknown';
                    // return ;
                    return '<a href="' . $whatsappUrl . '" target="_blank" class="inline-flex items-center gap-2 text-emerald-600 hover:text-emerald-700 transition-colors">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                        </svg>
                        <span>' . '<div class="font-medium text-gray-900">' . e($orderNo) . '</div>
                            <div class="text-sm text-gray-500">' . e($customer) . '</div>' . '</span>
                    </a>';
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
