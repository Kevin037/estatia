<?php

namespace App\Exports;

use App\Models\Milestone;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class MilestonesExport implements FromCollection, WithHeadings, WithMapping
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
        $query = Milestone::query();

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
            'Description',
            'Created At',
        ];
    }

    /**
     * @var Milestone $milestone
     */
    public function map($milestone): array
    {
        static $counter = 0;
        $counter++;

        return [
            $counter,
            $milestone->name,
            $milestone->desc ?? '-',
            $milestone->created_at ? $milestone->created_at->format('d M Y H:i') : '-',
        ];
    }
}
