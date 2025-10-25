<?php

namespace App\Exports;

use App\Models\Contractor;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ContractorsExport implements FromCollection, WithHeadings, WithMapping
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
        $query = Contractor::query();

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
            'Phone',
            'Created At',
        ];
    }

    public function map($contractor): array
    {
        static $index = 0;
        $index++;

        return [
            $index,
            $contractor->name,
            $contractor->phone ?? '-',
            $contractor->created_at->format('Y-m-d H:i:s'),
        ];
    }
}

