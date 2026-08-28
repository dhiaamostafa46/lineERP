<?php

namespace Modules\Store\App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class OpeningBalanceTemplateExport implements FromArray, WithHeadings, WithStyles, WithEvents
{
    public function array(): array
    {
        return [
            [
                '601',
                'كاميرا مراقبة HD لاسلكية',
                'إلكترونيات',
                'حبة',
                '10',
                '150.00',
                'المستودع الرئيسي',
                'Product'
            ],
            [
                '6200',
                'قميص رجالي رسمي - أبيض',
                'ملابس رجالية',
                'حبة',
                '0',
                '0.00',
                'المستودع الرئيسي',
                'Product'
            ],
            [
                '628001',
                'قميص رجالي رسمي - أبيض - M',
                'ملابس رجالية',
                'حبة',
                '15',
                '45.00',
                'المستودع الرئيسي',
                'Size'
            ],
            [
                '6281002',
                'قميص رجالي رسمي - أبيض - L',
                'ملابس رجالية',
                'حبة',
                '20',
                '45.00',
                'المستودع الرئيسي',
                'Size'
            ],
            [
                '6281101',
                'زيت زيتون بكر ممتاز 1 لتر',
                'مواد غذائية',
                'كرتون',
                '50',
                '120.00',
                'المستودع الرئيسي',
                'Product'
            ]
        ];
    }

    public function headings(): array
    {
        return [
            'الباركود (Barcode)',
            'اسم المنتج (Product Name) *',
            'التصنيف (Category) *',
            'الوحدة (Unit) *',
            'الكمية (Quantity) *',
            'تكلفة الوحدة (Unit Cost) *',
            'اسم المستودع (Store Name) *',
            'النوع (Type: Product/Size)',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '009EF7']
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
                
                foreach (range('A', 'H') as $columnID) {
                    $sheet->getColumnDimension($columnID)->setAutoSize(true);
                }

                // إضافة تعليق توضيحي
                $sheet->getComment('H1')->getText()->createTextRun('النوع: استخدم Product للمنتج العادي أو Size للمنتج الذي له مقاسات (يفصل الاسم بـ -)');
            },
        ];
    }
}
