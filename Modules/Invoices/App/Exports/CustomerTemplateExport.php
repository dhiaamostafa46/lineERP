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

class CustomerTemplateExport implements WithHeadings, WithStyles, WithTitle, FromArray, WithEvents
{
    public function title(): string
    {
        return __('invoices::models/inv_customers.plural');
    }

    public function headings(): array
    {
        return [
            [
                'الصف الثالث يحتوي على بيانات تجريبية للتوضيح. يرجى ملء البيانات ابتداءً من الصف الثالث. الحقول المميّزة بـ * هي حقول إجبارية. الاسم يكتب بالتنسيق (العربي - الإنجليزي) مثال: شركة أحمد - Ahmad Corp.',
                '', '', '', '', '', '', '', '', '', '', '', ''
            ],
            [
                __('invoices::models/inv_customers.fields.name') . ' (AR - EN) *',
                __('invoices::models/inv_customers.fields.phone'),
                __('invoices::models/inv_customers.fields.email'),
                __('invoices::models/inv_customers.fields.vat_number'),
                __('invoices::models/inv_customers.fields.cr_number'),
                __('invoices::models/inv_customers.fields.country'),
                __('invoices::models/inv_customers.fields.city'),
                __('invoices::models/inv_customers.fields.district'),
                __('invoices::models/inv_customers.fields.street'),
                __('invoices::models/inv_customers.fields.building_number'),
                __('invoices::models/inv_customers.fields.postal_code'),
                __('invoices::models/inv_customers.fields.additional_number'),
                __('invoices::models/inv_customers.fields.credit_limit')
            ]
        ];
    }

    public function array(): array
    {
        return [
            [
                'عميل تجريبي - Test Customer',
                '0501234567',
                'customer@test.com',
                '310123456700003',
                '1010123456',
                'السعودية',
                'الرياض',
                'الملز',
                'شارع صلاح الدين',
                '1234',
                '12345',
                '6789',
                '5000'
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
