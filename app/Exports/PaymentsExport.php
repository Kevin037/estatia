<?php

namespace App\Exports;

use App\Models\Payment;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PaymentsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    protected static $rowNumber = 0;

    /**
     * Constructor
     */
    public function __construct()
    {
        self::$rowNumber = 0;
    }

    /**
     * Get the collection of payments to export
     */
    public function collection()
    {
        return Payment::with(['invoice.order.customer', 'invoice.order.project'])
            ->orderBy('dt', 'desc')
            ->get();
    }

    /**
     * Define the column headings
     */
    public function headings(): array
    {
        return [
            'No',
            'Payment Number',
            'Payment Date',
            'Invoice Number',
            'Customer',
            'Project',
            'Payment Type',
            'Bank',
            'Amount (Rp)',
            'Created At'
        ];
    }

    /**
     * Map the data for each row
     */
    public function map($payment): array
    {
        self::$rowNumber++;

        return [
            self::$rowNumber,
            $payment->no ?? '-',
            $payment->dt ? $payment->dt->format('d/m/Y') : '-',
            $payment->invoice->no ?? '-',
            $payment->invoice->order->customer->name ?? '-',
            $payment->invoice->order->project->name ?? '-',
            ucfirst($payment->payment_type),
            $payment->bank ?? '-',
            number_format($payment->amount, 0, ',', '.'),
            $payment->created_at->format('d/m/Y H:i')
        ];
    }

    /**
     * Apply styles to the worksheet
     */
    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]]
        ];
    }

    /**
     * Set column widths
     */
    public function columnWidths(): array
    {
        return [
            'A' => 5,   // No
            'B' => 20,  // Payment Number
            'C' => 15,  // Payment Date
            'D' => 20,  // Invoice Number
            'E' => 30,  // Customer
            'F' => 25,  // Project
            'G' => 15,  // Payment Type
            'H' => 20,  // Bank
            'I' => 20,  // Amount
            'J' => 18,  // Created At
        ];
    }
}
