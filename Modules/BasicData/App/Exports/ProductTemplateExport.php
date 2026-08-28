<?php

namespace Modules\BasicData\App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use Maatwebsite\Excel\Concerns\FromArray;

class ProductTemplateExport implements WithHeadings, WithStyles, WithTitle, FromArray, WithEvents
{
    public function title(): string
    {
        return __('basicdata::models/db_products.plural');
    }

    public function headings(): array
    {
        return [[__('basicdata::models/db_products.import_instructions.line3') . ' | ' . __('basicdata::models/db_products.import_instructions.size_hint') . ' | ' . __('basicdata::models/db_products.import_instructions.unit_mandatory_hint'), '', '', '', '', '', '', '', ''], [__('basicdata::models/db_products.fields.barcode') . ' (Optional)', __('basicdata::models/db_products.fields.name') . ' (AR - EN) *', __('basicdata::models/db_products.fields.category_id') . ' *', __('basicdata::models/db_products.fields.prod_price') . ' *', __('basicdata::models/db_products.fields.cost_price') . ' *', __('basicdata::models/db_products.fields.unit_id') . ' *', __('basicdata::models/db_products.fields.min_quantity'), __('basicdata::models/db_products.fields.vat') . ' *', __('basicdata::models/db_products.fields.type') . ' (Product / Service / Size) *']];
    }

    public function array(): array
    {
        return [['12345678943', 'منتج  - Test Product', 'تصنيف عام', '100', '80', 'حبة', '5', '15', 'Product'], ['', 'خدمة تجريبية - Test Service', 'خدمات', '50', '0', 'ساعة', '0', '0', 'Service'], ['', 'منتج بمقاسات - Product with Sizes', 'ملابس', '200', '150', 'قطعة', '10', '15', 'Size']];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // كتابة الخيارات في أعمدة بعيدة وإخفائها لضمان عمل القائمة بشكل قطعي
                $sheet->setCellValue('Z1', 'Product');
                $sheet->setCellValue('Z2', 'Service');
                $sheet->setCellValue('Z3', 'Size');
                $sheet->getColumnDimension('Z')->setVisible(false);

                // إعداد التحقق الصارم من البيانات
                $validation = $sheet->getCell('I3')->getDataValidation();
                $validation->setType(DataValidation::TYPE_LIST);
                $validation->setErrorStyle(DataValidation::STYLE_STOP); // منع الإدخال تماماً في حال الخطأ
                $validation->setAllowBlank(true);
                $validation->setShowInputMessage(true);
                $validation->setShowErrorMessage(true);
                $validation->setShowDropDown(true);

                // رسائل تنبيه صارمة بالعربية والإنجليزية
                $validation->setErrorTitle('خطأ في النوع / Type Error');
                $validation->setError('يجب اختيار النوع من القائمة المنسدلة فقط. / You must select from the dropdown list only.');
                $validation->setPromptTitle('اختر النوع / Select Type');
                $validation->setPrompt('يرجى اختيار أحد الخيارات الثلاثة المتاحة. / Please choose one of the three available options.');

                // ربط القائمة بالخلايا المخفية
                $validation->setFormula1('$Z$1:$Z$3');

                // تطبيق التحقق على العمود I بالكامل لضمان عدم كتابة أي كلمة أخرى
                for ($i = 4; $i <= 1000; $i++) {
                    $sheet->getCell("I{$i}")->setDataValidation(clone $validation);
                }
            },
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // دمج الصف الأول للعنوان
        $sheet->mergeCells('A1:I1');

        // تنسيق عنوان التعليمات
        $sheet->getStyle('A1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 14,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'f49e0b'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
        ]);

        // تنسيق الهيدر (الصف الثاني)
        $sheet->getStyle('A2:I2')->applyFromArray([
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

        // تنسيق البيانات
        $sheet->getStyle('A3:I5')->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
            ],
        ]);

        // جعل الأعمدة تتكيف مع المحتوى
        foreach (range('A', 'I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        return [];
    }
}
