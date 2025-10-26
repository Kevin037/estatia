<?php

namespace App\Exports;

use App\Models\Unit;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class UnitsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    protected $projectId;
    protected $typeId;
    protected $status;
    protected $minPrice;
    protected $maxPrice;

    public function __construct($projectId = null, $typeId = null, $status = null, $minPrice = null, $maxPrice = null)
    {
        $this->projectId = $projectId;
        $this->typeId = $typeId;
        $this->status = $status;
        $this->minPrice = $minPrice;
        $this->maxPrice = $maxPrice;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $query = Unit::with(['cluster.project', 'product.type', 'sales']);

        // Apply filters
        if ($this->projectId) {
            $query->whereHas('cluster', function($q) {
                $q->where('project_id', $this->projectId);
            });
        }

        if ($this->typeId) {
            $query->whereHas('product', function($q) {
                $q->where('type_id', $this->typeId);
            });
        }

        if ($this->status) {
            $query->where('status', $this->status);
        }

        if ($this->minPrice) {
            $query->where('price', '>=', $this->minPrice);
        }

        if ($this->maxPrice) {
            $query->where('price', '<=', $this->maxPrice);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Unit Number',
            'Unit Name',
            'Project',
            'Cluster',
            'Product',
            'Type',
            'Price (Rp)',
            'Area (m²)',
            'Building Area (m²)',
            'Facilities',
            'Status',
            'Sales Person',
            'Created At',
        ];
    }

    public function map($unit): array
    {
        static $rowNumber = 0;
        $rowNumber++;

        return [
            $rowNumber,
            $unit->no,
            $unit->name,
            $unit->cluster->project->name ?? '-',
            $unit->cluster->name ?? '-',
            $unit->product->name ?? '-',
            $unit->product->type->name ?? '-',
            number_format($unit->price, 0, ',', '.'),
            $unit->area ?? '-',
            $unit->building_area ?? '-',
            $unit->facilities ?? '-',
            ucfirst($unit->status),
            $unit->sales->name ?? '-',
            $unit->created_at->format('d/m/Y H:i'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,
            'B' => 15,
            'C' => 20,
            'D' => 25,
            'E' => 20,
            'F' => 20,
            'G' => 15,
            'H' => 18,
            'I' => 12,
            'J' => 18,
            'K' => 30,
            'L' => 12,
            'M' => 20,
            'N' => 18,
        ];
    }
}
