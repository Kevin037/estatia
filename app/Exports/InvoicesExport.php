<?php

namespace App\Exports;

use App\Models\Invoice;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class InvoicesExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
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
     * Get the collection of invoices to export
     */
    public function collection()
    {
        return Invoice::with(['order.customer', 'order.project', 'order.cluster', 'order.unit', 'payments'])
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
            'Invoice Number',
            'Invoice Date',
            'Order Number',
            'Customer',
            'Project',
            'Cluster',
            'Unit Number',
            'Total (Rp)',
            'Payment Status',
            'Created At'
        ];
    }

    /**
     * Map the data for each row
     */
    public function map($invoice): array
    {
        self::$rowNumber++;

        return [
            self::$rowNumber,
            $invoice->no ?? '-',
            $invoice->dt ? $invoice->dt->format('d/m/Y') : '-',
            $invoice->order->no ?? '-',
            $invoice->order->customer->name ?? '-',
            $invoice->order->project->name ?? '-',
            $invoice->order->cluster->name ?? '-',
            $invoice->order->unit->no ?? '-',
            number_format($invoice->order->total ?? 0, 0, ',', '.'),
            ucfirst($invoice->payment_status),
            $invoice->created_at->format('d/m/Y H:i')
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
            'B' => 20,  // Invoice Number
            'C' => 15,  // Invoice Date
            'D' => 20,  // Order Number
            'E' => 30,  // Customer
            'F' => 25,  // Project
            'G' => 20,  // Cluster
            'H' => 15,  // Unit Number
            'I' => 20,  // Total
            'J' => 15,  // Payment Status
            'K' => 18,  // Created At
        ];
    }
}
