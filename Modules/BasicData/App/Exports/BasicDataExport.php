<?php

namespace Modules\BasicData\App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class BasicDataExport implements FromCollection, WithHeadings, WithStyles
{
    protected $data;
    protected $headers;

    public function __construct($data, $headers)
    {
        $this->data = $data;
        $this->headers = $headers;
    }

    public function collection()
    {
        return new Collection($this->data);
    }

    public function headings(): array
    {
        return $this->headers;
    }

    public function styles(Worksheet $sheet)
    {
        $rowCount = count($this->data) + 1;
        $colCount = count($this->headers);
        $lastColumn = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colCount);

        // Header Row Styling (Clean Enterprise Royal Blue)
        $sheet->getRowDimension(1)->setRowHeight(30);
        $sheet->getStyle("A1:{$lastColumn}1")->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 12,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '2563EB'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '1D4ED8'],
                ],
            ],
        ]);

        // Data Rows Styling (Clean White & Soft Slate Zebra with Light Borders)
        for ($row = 2; $row <= $rowCount; $row++) {
            $sheet->getRowDimension($row)->setRowHeight(22);
            $bgColor = ($row % 2 === 0) ? 'FFFFFF' : 'F8FAFC';
            
            $sheet->getStyle("A{$row}:{$lastColumn}{$row}")->applyFromArray([
                'font' => [
                    'size' => 11,
                    'color' => ['rgb' => '1E293B'],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => $bgColor],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'E2E8F0'],
                    ],
                ],
            ]);
        }

        // Auto-fit columns
        foreach (range(1, $colCount) as $col) {
            $sheet->getColumnDimensionByColumn($col)->setAutoSize(true);
        }

        return [];
    }
}
