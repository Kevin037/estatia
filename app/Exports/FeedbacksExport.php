<?php

namespace App\Exports;

use App\Models\Feedback;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class FeedbacksExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
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
     * Get the collection of feedbacks to export
     */
    public function collection()
    {
        $query = Feedback::with(['order.customer', 'order.project']);

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
            'Date',
            'Order Number',
            'Customer',
            'Project',
            'Description',
            'Created At'
        ];
    }

    /**
     * Map the data for each row
     */
    public function map($feedback): array
    {
        self::$rowNumber++;

        return [
            self::$rowNumber,
            $feedback->dt ? $feedback->dt->format('d/m/Y') : '-',
            $feedback->order->no ?? '-',
            $feedback->order->customer->name ?? '-',
            $feedback->order->project->name ?? '-',
            strip_tags($feedback->desc),
            $feedback->created_at->format('d/m/Y H:i')
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
            'B' => 15,  // Date
            'C' => 20,  // Order Number
            'D' => 30,  // Customer
            'E' => 25,  // Project
            'F' => 50,  // Description
            'G' => 18,  // Created At
        ];
    }
}
