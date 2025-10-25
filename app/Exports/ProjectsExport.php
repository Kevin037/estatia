<?php

namespace App\Exports;

use App\Models\Project;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProjectsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    protected $status;
    protected $startDate;
    protected $endDate;

    public function __construct($status = null, $startDate = null, $endDate = null)
    {
        $this->status = $status;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $query = Project::with(['land', 'contractors', 'clusters', 'units', 'projectMilestones.milestone']);

        if ($this->status) {
            $query->where('status', $this->status);
        }

        if ($this->startDate) {
            $query->whereDate('dt_start', '>=', $this->startDate);
        }

        if ($this->endDate) {
            $query->whereDate('dt_end', '<=', $this->endDate);
        }

        return $query->orderBy('dt_start', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Project Name',
            'Land/Location',
            'Start Date',
            'End Date',
            'Status',
            'Contractors',
            'Milestones',
            'Clusters',
            'Product Types',
            'Total Units',
            'Created At',
        ];
    }

    public function map($project): array
    {
        static $rowNumber = 0;
        $rowNumber++;

        // Get contractors
        $contractors = $project->contractors->pluck('name')->join(', ');

        // Get milestones
        $milestones = $project->projectMilestones->map(function($pm) {
            return $pm->milestone->name . ' (Target: ' . $pm->target_dt->format('d/m/Y') . ')';
        })->join('; ');

        // Get clusters
        $clusters = $project->clusters->pluck('name')->join(', ');

        // Get unique product count
        $productCount = $project->units()->distinct('product_id')->count('product_id');

        // Get total units
        $totalUnits = $project->units()->count();

        return [
            $rowNumber,
            $project->name,
            $project->land ? $project->land->address : '-',
            $project->dt_start->format('d/m/Y'),
            $project->dt_end->format('d/m/Y'),
            ucfirst(str_replace('_', ' ', $project->status)),
            $contractors ?: '-',
            $milestones ?: '-',
            $clusters ?: '-',
            $productCount,
            $totalUnits,
            $project->created_at->format('d/m/Y H:i'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'E5E7EB']]],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,
            'B' => 25,
            'C' => 30,
            'D' => 12,
            'E' => 12,
            'F' => 15,
            'G' => 30,
            'H' => 40,
            'I' => 25,
            'J' => 15,
            'K' => 12,
            'L' => 18,
        ];
    }
}
