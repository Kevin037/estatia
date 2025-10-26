<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Order;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\InvoicesExport;
use Maatwebsite\Excel\Facades\Excel;

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
                    $phoneNumber = preg_replace('/[^0-9]/', '', $invoice->order->customer->phone);
                    // Add country code if not present (assuming Indonesia +62)
                    if (!str_starts_with($phoneNumber, '62')) {
                        if (str_starts_with($phoneNumber, '0')) {
                            $phoneNumber = '62' . substr($phoneNumber, 1);
                        } else {
                            $phoneNumber = '62' . $phoneNumber;
                        }
                    }
                    $whatsappUrl = 'https://wa.me/' . $phoneNumber;
                    
                    return '<a href="' . $whatsappUrl . '" target="_blank" class="inline-flex items-center gap-2 text-emerald-600 hover:text-emerald-700 transition-colors">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                        </svg>
                        <span>' . e($invoice->order->customer->name ?? '-') . '</span>
                    </a>';
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
                ->rawColumns(['status', 'actions','customer'])
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

    /**
     * Export invoices to Excel
     */
    public function export()
    {
        return Excel::download(
            new InvoicesExport(),
            'invoices_' . now()->format('Y-m-d_His') . '.xlsx'
        );
    }
}
