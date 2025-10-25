<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Order;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Invoice::with(['order.customer', 'payments']);

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('invoice_no', function ($invoice) {
                    return $invoice->no;
                })
                ->addColumn('date', function ($invoice) {
                    return $invoice->dt ? $invoice->dt->format('d M Y') : '-';
                })
                ->addColumn('order_no', function ($invoice) {
                    return $invoice->order->no ?? '-';
                })
                ->addColumn('customer', function ($invoice) {
                    return $invoice->order->customer->name ?? '-';
                })
                ->addColumn('total', function ($invoice) {
                    return 'Rp ' . number_format($invoice->order->total ?? 0, 0, ',', '.');
                })
                ->addColumn('status', function ($invoice) {
                    $status = $invoice->payment_status;
                    $color = $status === 'paid' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800';
                    return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ' . $color . '">' . ucfirst($status) . '</span>';
                })
                ->addColumn('actions', function ($invoice) {
                    return view('invoices.actions', compact('invoice'))->render();
                })
                ->rawColumns(['status', 'actions'])
                ->make(true);
        }

        return view('invoices.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Get orders that don't have invoices yet
        $orders = Order::with(['customer', 'project', 'cluster', 'unit'])
            ->whereDoesntHave('invoices')
            ->orderBy('dt', 'desc')
            ->get();

        return view('invoices.create', compact('orders'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'dt' => 'required|date',
        ]);

        $invoice = Invoice::create([
            'no' => Invoice::generateNumber(),
            'order_id' => $request->order_id,
            'dt' => $request->dt,
            'status' => 'unpaid',
        ]);

        return redirect()->route('invoices.show', $invoice)
            ->with('success', 'Invoice created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Invoice $invoice)
    {
        $invoice->load([
            'order.customer',
            'order.project',
            'order.cluster',
            'order.unit.product.type',
            'order.unit.product.productPhotos',
            'order.unit.unitPhotos',
            'payments'
        ]);

        return view('invoices.show', compact('invoice'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Invoice $invoice)
    {
        // Get all orders
        $orders = Order::with(['customer', 'project', 'cluster', 'unit'])
            ->orderBy('dt', 'desc')
            ->get();

        $invoice->load([
            'order.customer',
            'order.project',
            'order.cluster',
            'order.unit.product.type',
            'order.unit.product.productPhotos',
            'order.unit.unitPhotos'
        ]);

        return view('invoices.edit', compact('invoice', 'orders'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Invoice $invoice)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'dt' => 'required|date',
        ]);

        $invoice->update([
            'order_id' => $request->order_id,
            'dt' => $request->dt,
        ]);

        return redirect()->route('invoices.show', $invoice)
            ->with('success', 'Invoice updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Invoice $invoice)
    {
        $invoice->delete();

        return redirect()->route('invoices.index')
            ->with('success', 'Invoice deleted successfully.');
    }

    /**
     * Get order details via Ajax
     */
    public function getOrderDetails(Request $request)
    {
        $order = Order::with([
            'customer',
            'project',
            'cluster',
            'unit.product.type',
            'unit.product.productPhotos',
            'unit.unitPhotos'
        ])->find($request->order_id);

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found'
            ], 404);
        }

        $data = [
            'order_no' => $order->no,
            'date' => $order->dt?->format('d M Y'),
            'customer' => [
                'name' => $order->customer->name ?? '-',
                'email' => $order->customer->email ?? '-',
                'phone' => $order->customer->phone ?? '-',
            ],
            'project' => $order->project->name ?? '-',
            'cluster' => $order->cluster->name ?? '-',
            'unit_no' => $order->unit->no ?? '-',
            'total' => $order->total,
            'total_formatted' => number_format($order->total, 0, ',', '.'),
            'product_photos' => $order->unit && $order->unit->product ? 
                $order->unit->product->productPhotos->map(function ($photo) {
                    return [
                        'url' => $photo->photo_url,
                        'name' => $photo->name ?? 'Product Photo'
                    ];
                }) : [],
            'unit_photos' => $order->unit ? 
                $order->unit->unitPhotos->map(function ($photo) {
                    return [
                        'url' => $photo->photo_url,
                        'name' => $photo->name ?? 'Unit Photo'
                    ];
                }) : [],
        ];

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * Export invoice to PDF
     */
    public function exportPdf(Invoice $invoice)
    {
        $invoice->load([
            'order.customer',
            'order.project',
            'order.cluster',
            'order.unit.product.type',
            'payments'
        ]);

        $pdf = Pdf::loadView('invoices.pdf', compact('invoice'));
        
        return $pdf->download('invoice-' . $invoice->no . '.pdf');
    }
}
