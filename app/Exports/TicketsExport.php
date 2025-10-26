<?php

namespace App\Exports;

use App\Models\Ticket;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TicketsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    protected $startDate;
    protected $endDate;
    protected static $rowNumber = 0;

    /**
     * Constructor to accept filter parameters
     */
    public function __construct($startDate = null, $endDate = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        self::$rowNumber = 0;
    }

    /**
     * Get the collection of tickets to export
     */
    public function collection()
    {
        $query = Ticket::with(['order.customer', 'order.project']);

        // Apply date range filter if provided
        if ($this->startDate && $this->endDate) {
            $query->dateRange($this->startDate, $this->endDate);
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
            'Ticket Number',
            'Date',
            'Order Number',
            'Customer',
            'Project',
            'Title',
            'Description',
            'Status',
            'Created At'
        ];
    }

    /**
     * Map the data for each row
     */
    public function map($ticket): array
    {
        self::$rowNumber++;

        return [
            self::$rowNumber,
            $ticket->no ?? '-',
            $ticket->dt ? $ticket->dt->format('d/m/Y') : '-',
            $ticket->order->no ?? '-',
            $ticket->order->customer->name ?? '-',
            $ticket->order->project->name ?? '-',
            $ticket->title,
            strip_tags($ticket->desc),
            ucfirst($ticket->status),
            $ticket->created_at->format('d/m/Y H:i')
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
            'B' => 20,  // Ticket Number
            'C' => 15,  // Date
            'D' => 20,  // Order Number
            'E' => 30,  // Customer
            'F' => 25,  // Project
            'G' => 30,  // Title
            'H' => 50,  // Description
            'I' => 12,  // Status
            'J' => 18,  // Created At
        ];
    }
}
