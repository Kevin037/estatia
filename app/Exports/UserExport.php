<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class UserExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithDrawings, WithColumnWidths
{
    protected $startDate;
    protected $endDate;
    protected $rowNumber = 0;
    protected $users;

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
        $query = User::query();

        // Apply date range filter if provided
        if ($this->startDate && $this->endDate) {
            $query->whereBetween('created_at', [
                $this->startDate . ' 00:00:00',
                $this->endDate . ' 23:59:59'
            ]);
        }

        $this->users = $query->orderBy('name')->get();
        
        return $this->users;
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'No',
            'Name',
            'Email',
            'Phone',
            'Photo',
            'Registered At'
        ];
    }

    /**
     * @param User $user
     */
    public function map($user): array
    {
        $this->rowNumber++;
        
        return [
            $this->rowNumber,
            $user->name,
            $user->email,
            $user->phone ?? '-',
            '', // Photo will be added via drawings
            $user->created_at->format('d M Y H:i')
        ];
    }

    /**
     * @return array
     */
    public function drawings()
    {
        $drawings = [];
        $row = 2; // Start from row 2 (after heading)

        foreach ($this->users as $user) {
            if ($user->photo && file_exists(storage_path('app/public/' . $user->photo))) {
                $drawing = new Drawing();
                $drawing->setName($user->name);
                $drawing->setDescription('User Photo');
                $drawing->setPath(storage_path('app/public/' . $user->photo));
                $drawing->setHeight(50);
                $drawing->setWidth(50);
                $drawing->setCoordinates('E' . $row);
                $drawing->setOffsetX(5);
                $drawing->setOffsetY(5);
                
                $drawings[] = $drawing;
            }
            
            $row++;
        }

        return $drawings;
    }

    /**
     * @param Worksheet $sheet
     */
    public function styles(Worksheet $sheet)
    {
        // Set row height for all data rows to accommodate images
        foreach ($this->users as $index => $user) {
            $rowNumber = $index + 2; // +2 because row 1 is heading
            $sheet->getRowDimension($rowNumber)->setRowHeight(60);
        }

        return [
            // Style the first row as bold text
            1 => [
                'font' => ['bold' => true, 'size' => 12],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '059669']
                ],
                'font' => ['color' => ['rgb' => 'FFFFFF'], 'bold' => true]
            ],
        ];
    }

    /**
     * @return array
     */
    public function columnWidths(): array
    {
        return [
            'A' => 8,   // No
            'B' => 25,  // Name
            'C' => 30,  // Email
            'D' => 20,  // Phone
            'E' => 15,  // Photo
            'F' => 20,  // Registered At
        ];
    }
}
