<?php

namespace App\Exports;

use App\Models\Land;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class LandsExport implements FromCollection, WithHeadings, WithMapping
{
    protected $startDate;
    protected $endDate;
    protected $rowNumber = 0;

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
        $query = Land::query();

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
            'Address',
            'Width (m)',
            'Length (m)',
            'Location',
            'Description',
            'Created At'
        ];
    }

    /**
     * @param Land $land
     */
    public function map($land): array
    {
        $this->rowNumber++;
        
        return [
            $this->rowNumber,
            $land->name,
            $land->address,
            number_format($land->wide, 2),
            number_format($land->length, 2),
            $land->location ?? '-',
            $land->desc ?? '-',
            $land->created_at->format('d M Y H:i')
        ];
    }
}
