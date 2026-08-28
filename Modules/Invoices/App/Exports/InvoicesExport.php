<?php

namespace Modules\Invoices\App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class InvoicesExport implements FromCollection, WithHeadings, WithStyles
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
        // عدد الأعمدة والصفوف
        $rowCount = count($this->data) + 1;
        $colCount = count($this->headers);
        $lastColumn = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colCount);

        // تنسيق الهيدر
        $sheet->getStyle("A1:{$lastColumn}1")->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'], // نص أبيض
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '9ABF80'], // خلفية خضراء
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        // تنسيق باقي الصفوف (الخلفية الفاتحة)
        $sheet->getStyle("A2:{$lastColumn}{$rowCount}")->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E5E3D4'], // خلفية فاتحة
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        // جعل الأعمدة تتكيف مع المحتوى
        foreach (range(1, $colCount) as $col) {
            $sheet->getColumnDimensionByColumn($col)->setAutoSize(true);
        }

        return [];
    }
}
