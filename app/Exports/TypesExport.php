<?php

namespace App\Exports;

use App\Models\Type;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TypesExport implements FromCollection, WithHeadings, WithMapping
{
    protected $startDate;
    protected $endDate;

    public function __construct($startDate = null, $endDate = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function collection()
    {
        $query = Type::query();

        if ($this->startDate && $this->endDate) {
            $query->whereBetween('created_at', [
                $this->startDate . ' 00:00:00',
                $this->endDate . ' 23:59:59'
            ]);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Name',
            'Land Area (m²)',
            'Building Area (m²)',
            'Created At',
        ];
    }

    public function map($type): array
    {
        static $index = 0;
        $index++;

        return [
            $index,
            $type->name,
            number_format($type->land_area, 2),
            number_format($type->building_area, 2),
            $type->created_at->format('Y-m-d H:i:s'),
        ];
    }
}

