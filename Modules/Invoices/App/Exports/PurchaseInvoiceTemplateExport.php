<?php

namespace Modules\Invoices\App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class PurchaseInvoiceTemplateExport implements FromArray, WithHeadings, WithStyles, WithEvents
{
    public function array(): array
    {
        return [
            [
                '62811001',
                'كاميرا مراقبة HD لاسلكية',
                'شركة التقنية المتقدمة',
                'حبة',
                '10',
                '150.00',
                'المستودع الرئيسي',
                '15',
                'INV-2024-001',
                'Product'
            ],
            [
                '62811002',
                'زيت زيتون بكر ممتاز 1 لتر',
                'مؤسسة النور للتجارة',
                'كرتون',
                '50',
                '120.00',
                'المستودع الرئيسي',
                '15',
                'SUP-9988',
                'Product'
            ],
            [
                '62811003',
                'تيشيرت قطني - أبيض - XL',
                'مصنع الأناقة',
                'حبة',
                '100',
                '35.00',
                'المستودع الرئيسي',
                '15',
                'FAC-101',
                'Size'
            ],
        ];
    }

    public function headings(): array
    {
        return [
            'الباركود (Barcode)',
            'اسم المنتج (Product Name) *',
            'اسم المورد (Supplier Name) *',
            'الوحدة (Unit) *',
            'الكمية (Quantity) *',
            'سعر الوحدة (Unit Price) *',
            'اسم المستودع (Store Name) *',
            'نسبة الضريبة (VAT Rate %) *',
            'رقم فاتورة المورد (Supplier Inv No)',
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
                
                foreach (range('A', 'J') as $columnID) {
                    $sheet->getColumnDimension($columnID)->setAutoSize(true);
                }

                // إضافة تعليقات توضيحية
                $sheet->getComment('B1')->getText()->createTextRun('يجب أن يكون اسم المنتج مطابقاً لما هو مسجل في النظام.');
                $sheet->getComment('C1')->getText()->createTextRun('يجب أن يكون اسم المورد مطابقاً لما هو مسجل في النظام.');
                $sheet->getComment('J1')->getText()->createTextRun('النوع: استخدم Product للمنتج العادي أو Size للمنتج الذي له مقاسات (يفصل الاسم بـ -)');
            },
        ];
    }
}
