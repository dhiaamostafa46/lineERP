<?php

namespace Modules\Invoices\App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class SupplierTemplateExport implements WithHeadings, WithStyles, WithTitle, FromArray, WithEvents
{
    public function title(): string
    {
        return __('invoices::models/inv_suppliers.plural');
    }

    public function headings(): array
    {
        return [
            [
                'الصف الثالث يحتوي على بيانات تجريبية للتوضيح. يرجى ملء البيانات ابتداءً من الصف الثالث. الحقول المميّزة بـ * هي حقول إجبارية. الاسم يكتب بالتنسيق (العربي - الإنجليزي) مثال: شركة أحمد - Ahmad Corp.',
                '', '', '', '', '', '', '', '', '', '', '', ''
            ],
            [
                __('invoices::models/inv_suppliers.fields.name') . ' (AR - EN) *',
                __('invoices::models/inv_suppliers.fields.phone'),
                __('invoices::models/inv_suppliers.fields.email'),
                __('invoices::models/inv_suppliers.fields.vat_number'),
                __('invoices::models/inv_suppliers.fields.cr_number'),
                __('invoices::models/inv_suppliers.fields.country'),
                __('invoices::models/inv_suppliers.fields.city'),
                __('invoices::models/inv_suppliers.fields.district'),
                __('invoices::models/inv_suppliers.fields.street'),
                __('invoices::models/inv_suppliers.fields.building_number'),
                __('invoices::models/inv_suppliers.fields.postal_code'),
                __('invoices::models/inv_suppliers.fields.additional_number'),
                __('invoices::models/inv_suppliers.fields.credit_limit')
            ]
        ];
    }

    public function array(): array
    {
        return [
            [
                'مورد تجريبي - Test Supplier',
                '0509876543',
                'supplier@test.com',
                '310987654300003',
                '1010987654',
                'السعودية',
                'جدة',
                'الروضة',
                'شارع التحلية',
                '5678',
                '54321',
                '9876',
                '10000'
            ]
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $sheet->setRightToLeft(app()->getLocale() == 'ar');
            },
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // دمج الصف الأول للعنوان
        $sheet->mergeCells('A1:M1');

        // تنسيق عنوان التعليمات
        $sheet->getStyle('A1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 12,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'f49e0b'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true
            ],
        ]);

        $sheet->getRowDimension(1)->setRowHeight(40);

        // تنسيق الهيدر (الصف الثاني)
        $sheet->getStyle('A2:M2')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '9ABF80'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ]);

        $sheet->getRowDimension(2)->setRowHeight(25);

        // تنسيق البيانات
        $sheet->getStyle('A3:M3')->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
        ]);

        // جعل الأعمدة تتكيف مع المحتوى
        foreach (range('A', 'M') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        return [];
    }
}
