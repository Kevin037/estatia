<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class OrdersExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    protected $projectId;
    protected static $rowNumber = 0;

    /**
     * Constructor to accept filter parameters
     */
    public function __construct($projectId = null)
    {
        $this->projectId = $projectId;
        self::$rowNumber = 0;
    }

    /**
     * Get the collection of orders to export
     */
    public function collection()
    {
        $query = Order::with(['customer', 'project', 'cluster', 'unit']);

        // Apply project filter
        if ($this->projectId) {
            $query->where('project_id', $this->projectId);
        }

        return $query->orderBy('dt', 'desc')->get();
    }

    /**
     * Define the column headings
     */
    public function headings(): array
    {
        return [
            'No',
            'Order Number',
            'Date',
            'Customer',
            'Project',
            'Cluster',
            'Unit Number',
            'Total (Rp)',
            'Status',
            'Notes',
            'Created At'
        ];
    }

    /**
     * Map the data for each row
     */
    public function map($order): array
    {
        self::$rowNumber++;

        return [
            self::$rowNumber,
            $order->no ?? '-',
            $order->dt ? $order->dt->format('d/m/Y') : '-',
            $order->customer->name ?? '-',
            $order->project->name ?? '-',
            $order->cluster->name ?? '-',
            $order->unit->no ?? '-',
            number_format($order->total, 0, ',', '.'),
            ucfirst($order->status),
            $order->notes ?? '-',
            $order->created_at->format('d/m/Y H:i')
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
            'B' => 20,  // Order Number
            'C' => 15,  // Date
            'D' => 30,  // Customer
            'E' => 25,  // Project
            'F' => 20,  // Cluster
            'G' => 15,  // Unit Number
            'H' => 20,  // Total
            'I' => 12,  // Status
            'J' => 40,  // Notes
            'K' => 18,  // Created At
        ];
    }
}
