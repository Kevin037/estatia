<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Invoice;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Yajra\DataTables\Facades\DataTables;
use App\Exports\PaymentsExport;
use Maatwebsite\Excel\Facades\Excel;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $payments = Payment::with(['invoice.order.customer'])
                ->select('payments.*');

            return DataTables::of($payments)
                ->addIndexColumn()
                ->addColumn('payment_no', function ($payment) {
                    return $payment->no ?? 'N/A';
                })
                ->addColumn('date', function ($payment) {
                    return $payment->dt ? $payment->dt->format('d M Y') : 'N/A';
                })
                ->addColumn('invoice_no', function ($payment) {
                    return $payment->invoice->no ?? 'N/A';
                })
                ->addColumn('payment_type', function ($payment) {
                    return ucfirst($payment->payment_type);
                })
                ->addColumn('amount', function ($payment) {
                    return 'Rp ' . number_format($payment->amount, 0, ',', '.');
                })
                ->addColumn('actions', function ($payment) {
                    return view('payments.actions', compact('payment'))->render();
                })
                ->rawColumns(['actions'])
                ->make(true);
        }

        return view('payments.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Get all invoices and filter those that are not fully paid
        $invoices = Invoice::with(['order.customer', 'payments'])
            ->get()
            ->filter(function ($invoice) {
                $totalPaid = $invoice->payments->sum('amount');
                $orderTotal = $invoice->order->total ?? 0;
                return $totalPaid < $orderTotal;
            });

        return view('payments.create', compact('invoices'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $rules = [
            'invoice_id' => 'required|exists:invoices,id',
            'dt' => 'required|date',
            'payment_type' => 'required|in:cash,transfer',
            'amount' => 'required|numeric|min:0',
        ];

        // Add bank field validation only for transfer type
        if ($request->payment_type === 'transfer') {
            $rules['bank_account_id'] = 'required|string|max:255';
            $rules['bank_account_type'] = 'required|string|max:255';
            $rules['bank_account_name'] = 'required|string|max:255';
        }

        $validated = $request->validate($rules);

        DB::beginTransaction();
        try {
            // Generate payment number
            $validated['no'] = Payment::generateNumber();
            
            // Set paid_at to current timestamp
            $validated['paid_at'] = now();

            // Create payment
            $payment = Payment::create($validated);

            // Get the invoice with order and unit details
            $invoice = Invoice::with(['order.unit.product'])->find($request->invoice_id);

            // Reduce product stock quantity
            if ($invoice && $invoice->order && $invoice->order->unit && $invoice->order->unit->product) {
                $product = $invoice->order->unit->product;
                
                // Reduce qty by 1 (one unit sold)
                $product->decrement('qty', 1);
            }

            DB::commit();

            return redirect()->route('payments.index')
                ->with('success', 'Payment created successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create payment: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Payment $payment)
    {
        $payment->load([
            'invoice.order.customer',
            'invoice.order.project',
            'invoice.order.cluster',
            'invoice.order.unit.product.type'
        ]);

        return view('payments.show', compact('payment'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Payment $payment)
    {
        // Get all invoices and filter those that are not fully paid (including current payment's invoice)
        $invoices = Invoice::with(['order.customer', 'payments'])
            ->get()
            ->filter(function ($invoice) use ($payment) {
                $totalPaid = $invoice->payments->sum('amount');
                $orderTotal = $invoice->order->total ?? 0;
                // Include current payment's invoice or invoices that are not fully paid
                return $invoice->id === $payment->invoice_id || $totalPaid < $orderTotal;
            });

        return view('payments.edit', compact('payment', 'invoices'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Payment $payment)
    {
        $rules = [
            'invoice_id' => 'required|exists:invoices,id',
            'dt' => 'required|date',
            'payment_type' => 'required|in:cash,transfer',
            'amount' => 'required|numeric|min:0',
        ];

        // Add bank field validation only for transfer type
        if ($request->payment_type === 'transfer') {
            $rules['bank_account_id'] = 'required|string|max:255';
            $rules['bank_account_type'] = 'required|string|max:255';
            $rules['bank_account_name'] = 'required|string|max:255';
        }

        $validated = $request->validate($rules);

        // Update paid_at if payment date changed
        if ($request->dt !== $payment->dt?->format('Y-m-d')) {
            $validated['paid_at'] = now();
        }

        $payment->update($validated);

        return redirect()->route('payments.index')
            ->with('success', 'Payment updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Payment $payment)
    {
        DB::beginTransaction();
        try {
            // Get the invoice with order and unit details before deleting
            $invoice = Invoice::with(['order.unit.product'])->find($payment->invoice_id);

            // Restore product stock quantity
            if ($invoice && $invoice->order && $invoice->order->unit && $invoice->order->unit->product) {
                $product = $invoice->order->unit->product;
                
                // Restore qty by 1
                $product->increment('qty', 1);
            }

            $payment->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Payment deleted successfully.'
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete payment: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get invoice details via Ajax
     */
    public function getInvoiceDetails(Request $request)
    {
        $invoice = Invoice::with([
            'order.customer',
            'order.project',
            'order.cluster',
            'order.unit.product.type'
        ])->find($request->invoice_id);

        if (!$invoice) {
            return response()->json([
                'success' => false,
                'message' => 'Invoice not found'
            ], 404);
        }

        $data = [
            'invoice_no' => $invoice->no,
            'invoice_date' => $invoice->dt?->format('d M Y'),
            'order_no' => $invoice->order->no ?? 'N/A',
            'order_date' => $invoice->order->dt?->format('d M Y'),
            'customer' => [
                'name' => $invoice->order->customer->name ?? '-',
                'email' => $invoice->order->customer->email ?? '-',
                'phone' => $invoice->order->customer->phone ?? '-',
            ],
            'project' => $invoice->order->project->name ?? '-',
            'cluster' => $invoice->order->cluster->name ?? '-',
            'unit_no' => $invoice->order->unit->no ?? '-',
            'product_type' => $invoice->order->unit->product->type->name ?? '-',
            'total' => $invoice->order->total,
            'total_formatted' => number_format($invoice->order->total, 0, ',', '.'),
            'total_paid' => $invoice->total_paid,
            'total_paid_formatted' => number_format($invoice->total_paid, 0, ',', '.'),
            'remaining' => $invoice->order->total - $invoice->total_paid,
            'remaining_formatted' => number_format($invoice->order->total - $invoice->total_paid, 0, ',', '.'),
        ];

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * Export payment to PDF
     */
    public function exportPdf(Payment $payment)
    {
        $payment->load([
            'invoice.order.customer',
            'invoice.order.project',
            'invoice.order.cluster',
            'invoice.order.unit.product.type'
        ]);

        $pdf = Pdf::loadView('payments.pdf', compact('payment'));
        
        return $pdf->download('payment-' . $payment->no . '.pdf');
    }

    /**
     * Export payments to Excel
     */
    public function export()
    {
        return Excel::download(
            new PaymentsExport(),
            'payments_' . now()->format('Y-m-d_His') . '.xlsx'
        );
    }
}
