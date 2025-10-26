<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use App\Exports\TicketsExport;
use Maatwebsite\Excel\Facades\Excel;

class TicketController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $tickets = Ticket::with(['order.customer'])
                ->select('tickets.*');

            // Apply date filter if provided
            if ($request->filled('start_date') && $request->filled('end_date')) {
                $tickets->dateRange($request->start_date, $request->end_date);
            }

            return DataTables::of($tickets)
                ->addIndexColumn()
                ->addColumn('ticket_no', function ($ticket) {
                    return $ticket->no ?? 'N/A';
                })
                ->addColumn('date', function ($ticket) {
                    return $ticket->dt ? $ticket->dt->format('d M Y') : 'N/A';
                })
                ->addColumn('title', function ($ticket) {
                    return '<div class="font-medium text-gray-900">' . e($ticket->title) . '</div>';
                })
                ->addColumn('order_no', function ($ticket) {
                    $orderNo = $ticket->order->no ?? 'N/A';
                    $customer = $ticket->order->customer->name ?? 'Unknown';
                    return '<div class="font-medium text-gray-900">' . e($orderNo) . '</div>
                            <div class="text-sm text-gray-500">' . e($customer) . '</div>';
                })
                ->addColumn('status', function ($ticket) {
                    return '<select class="status-select form-select-sm text-xs py-1 px-2 rounded" data-id="' . $ticket->id . '" data-url="' . route('tickets.update-status', $ticket->id) . '">
                                <option value="pending" ' . ($ticket->status === 'pending' ? 'selected' : '') . '>Pending</option>
                                <option value="completed" ' . ($ticket->status === 'completed' ? 'selected' : '') . '>Completed</option>
                            </select>';
                })
                ->addColumn('action', function ($ticket) {
                    return view('tickets.partials.actions', compact('ticket'))->render();
                })
                ->rawColumns(['title', 'order_no', 'status', 'action'])
                ->make(true);
        }

        return view('tickets.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $orders = Order::with('customer')->get();

        return view('tickets.create', compact('orders'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'dt' => 'required|date',
            'title' => 'required|string|max:255',
            'desc' => 'required|string',
            'photo' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
            'status' => 'nullable|in:pending,completed',
        ]);

        // Generate ticket number
        $validated['no'] = Ticket::generateNumber();

        // Set default status
        $validated['status'] = $validated['status'] ?? 'pending';

        // Handle photo upload
        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('tickets', 'public');
        }

        Ticket::create($validated);

        return redirect()->route('tickets.index')
            ->with('success', 'Ticket created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Ticket $ticket)
    {
        $ticket->load([
            'order.customer',
            'order.project',
            'order.cluster',
            'order.unit.product'
        ]);

        return view('tickets.show', compact('ticket'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Ticket $ticket)
    {
        $orders = Order::with('customer')
            ->where('status', 'completed')
            ->orWhere('id', $ticket->order_id)
            ->get();

        return view('tickets.edit', compact('ticket', 'orders'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Ticket $ticket)
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'dt' => 'required|date',
            'title' => 'required|string|max:255',
            'desc' => 'required|string',
            'photo' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
            'status' => 'nullable|in:pending,completed',
        ]);

        // Handle photo upload
        if ($request->hasFile('photo')) {
            // Delete old photo
            if ($ticket->photo && Storage::disk('public')->exists($ticket->photo)) {
                Storage::disk('public')->delete($ticket->photo);
            }
            $validated['photo'] = $request->file('photo')->store('tickets', 'public');
        }

        $ticket->update($validated);

        return redirect()->route('tickets.index')
            ->with('success', 'Ticket updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ticket $ticket)
    {
        // Delete photo if exists
        if ($ticket->photo && Storage::disk('public')->exists($ticket->photo)) {
            Storage::disk('public')->delete($ticket->photo);
        }

        $ticket->delete();

        return response()->json([
            'success' => true,
            'message' => 'Ticket deleted successfully!'
        ]);
    }

    /**
     * Update ticket status
     */
    public function updateStatus(Request $request, Ticket $ticket)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,completed',
        ]);

        $ticket->update(['status' => $validated['status']]);

        return response()->json([
            'success' => true,
            'message' => 'Ticket status updated successfully!'
        ]);
    }

    /**
     * Export tickets to Excel
     */
    public function export(Request $request)
    {
        return Excel::download(
            new TicketsExport($request->start_date, $request->end_date),
            'tickets_' . now()->format('Y-m-d_His') . '.xlsx'
        );
    }
}
