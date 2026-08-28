<?php

namespace Modules\BasicData\App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class ProductImportErrorsExport implements FromArray, WithHeadings, WithStyles, WithEvents
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
            // تأكد من أن الصف يحتوي على كل الأعمدة (حتى لو كانت فارغة)
            $rowData = array_pad($row, 9, '');
            // إضافة رسالة الخطأ في العمود الأخير
            $rowData[] = $error['error'];
            $data[] = $rowData;
        }
        return $data;
    }

    public function headings(): array
    {
        return [
            __('basicdata::models/db_products.fields.barcode'),
            __('basicdata::models/db_products.fields.name') . ' (AR-EN)',
            __('basicdata::models/db_products.fields.category_id'),
            __('basicdata::models/db_products.fields.prod_price'),
            __('basicdata::models/db_products.fields.cost_price'),
            __('basicdata::models/db_products.fields.unit_id'),
            __('basicdata::models/db_products.fields.min_quantity'),
            __('basicdata::models/db_products.fields.vat'),
            __('basicdata::models/db_products.fields.type'),
            __('crud.error'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'F1416C'] // Red for errors
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
                
                // تحسين عرض الأعمدة
                foreach (range('A', 'J') as $columnID) {
                    $sheet->getColumnDimension($columnID)->setAutoSize(true);
                }

                // تمييز عمود الخطأ باللون الأحمر الخفيف
                $highestRow = $sheet->getHighestRow();
                if ($highestRow > 1) {
                    $sheet->getStyle('J2:J' . $highestRow)->getFont()->getColor()->setRGB('F1416C');
                }
            },
        ];
    }
}
