<?php

namespace Modules\Store\App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class OpeningBalanceImportErrorsExport implements FromArray, WithHeadings, WithStyles, WithEvents
{
    protected $errors;

    public function __construct(array $errors)
    {
        $this->errors = $errors;
    }

    public function array(): array
    {
        $data = [];
        foreach ($this->errors as $error) {
            $row = $error['row'];
            $row[] = $error['error']; // Add error message as last column
            $data[] = $row;
        }
        return $data;
    }

    public function headings(): array
    {
        return [
            'الباركود (Barcode)',
            'اسم المنتج (Product Name)',
            'التصنيف (Category)',
            'الوحدة (Unit)',
            'الكمية (Quantity)',
            'تكلفة الوحدة (Unit Cost)',
            'اسم المستودع (Store Name)',
            'النوع (Type)',
            'رسالة الخطأ (Error Message)',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E42828'] // Red for error sheet
                ],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $sheet->setRightToLeft(true);
                
                foreach (range('A', 'I') as $columnID) {
                    $sheet->getColumnDimension($columnID)->setAutoSize(true);
                }

                // Highlight the error column
                $lastRow = count($this->errors) + 1;
                $sheet->getStyle('I2:I' . $lastRow)->getFont()->getColor()->setRGB('E42828');
            },
        ];
    }
}
