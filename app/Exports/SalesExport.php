<?php

namespace App\Exports;

use App\Models\Sale;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SalesExport implements FromCollection, WithHeadings, WithMapping
{
    protected $startDate;
    protected $endDate;

    public function __construct($startDate = null, $endDate = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $query = Sale::query();

        // Apply date range filter if provided
        if ($this->startDate && $this->endDate) {
            $query->whereBetween('created_at', [
                $this->startDate . ' 00:00:00',
                $this->endDate . ' 23:59:59'
            ]);
        }

        return $query->orderBy('name')->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'No',
            'Name',
            'Phone Number',
            'Created At',
        ];
    }

    /**
     * @var Sale $sale
     */
    public function map($sale): array
    {
        static $counter = 0;
        $counter++;

        return [
            $counter,
            $sale->name,
            $sale->phone ?? '-',
            $sale->created_at ? $sale->created_at->format('d M Y H:i') : '-',
        ];
    }
}
