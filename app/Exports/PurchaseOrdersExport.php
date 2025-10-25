<?php

namespace App\Exports;

use App\Models\PurchaseOrder;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PurchaseOrdersExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    protected $startDate;
    protected $endDate;
    protected $status;

    public function __construct($startDate = null, $endDate = null, $status = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->status = $status;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $query = PurchaseOrder::with(['project', 'supplier', 'details.material'])
            ->orderBy('dt', 'desc');

        if ($this->startDate && $this->endDate) {
            $query->dateRange($this->startDate, $this->endDate);
        }

        if ($this->status) {
            $query->byStatus($this->status);
        }

        return $query->get();
    }

    /**
    * @return array
    */
    public function headings(): array
    {
        return [
            'No',
            'Transaction No',
            'Project',
            'Supplier',
            'Date',
            'Total',
            'Status',
            'Materials',
            'Created At',
        ];
    }

    /**
    * @var PurchaseOrder $purchaseOrder
    */
    public function map($purchaseOrder): array
    {
        static $rowNumber = 0;
        $rowNumber++;

        // Get materials list
        $materials = $purchaseOrder->details->map(function ($detail) {
            return $detail->material->name . ' (' . $detail->qty . ')';
        })->join(', ');

        return [
            $rowNumber,
            $purchaseOrder->no ?? '-',
            $purchaseOrder->project ? $purchaseOrder->project->name : '-',
            $purchaseOrder->supplier ? $purchaseOrder->supplier->name : '-',
            $purchaseOrder->dt ? $purchaseOrder->dt->format('d M Y') : '-',
            'Rp ' . number_format($purchaseOrder->total, 0, ',', '.'),
            ucfirst($purchaseOrder->status),
            $materials,
            $purchaseOrder->created_at->format('d M Y H:i'),
        ];
    }

    /**
    * @return array
    */
    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E2E8F0']
                ]
            ],
        ];
    }

    /**
    * @return array
    */
    public function columnWidths(): array
    {
        return [
            'A' => 8,
            'B' => 20,
            'C' => 25,
            'D' => 25,
            'E' => 15,
            'F' => 18,
            'G' => 12,
            'H' => 40,
            'I' => 20,
        ];
    }
}
