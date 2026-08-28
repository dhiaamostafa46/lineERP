<?php

namespace Modules\HR\App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class HrFailedEmployeesExport implements FromArray, WithHeadings, ShouldAutoSize, WithStyles
{
    private $failedRows;

    public function __construct(array $failedRows)
    {
        $this->failedRows = $failedRows;
    }

    public function array(): array
    {
        // التأكد من تحويل البيانات إلى مصفوفة قيم مرتبة لتطابق العناوين
        return array_map(function($row) {
            return array_values($row);
        }, $this->failedRows);
    }

    public function headings(): array
    {
        return [
            'الاسم الكامل',
            'ملخص الاسم',
            'رقم الجوال',
            'الايميل (الشركة)',
            'الايميل (الشخصي)',
            'الرقم الوظيفي',
            'القسم',
            'الوظيفة',
            'الدوام',
            'تاريخ الميلاد',
            'تاريخ المباشرة',
            'الآيبان',
            'الفرع',
            'العنوان',
            'العنوان الوطني',
            'الديانة',
            'الجنسية',
            'الحد الأقصى لأيام الإجازة',
            'رصيد الإجازات',
            'الحد الأقصى للسلف',
            'المستوى الوظيفي',
            'التخصص',
            'الجنس',
            'الحالة الاجتماعية',
            'رقم الهوية',
            'تاريخ انتهاء الهوية',
            'التأمين',
            'تاريخ التأمين',
            'تاريخ انتهاء الرخصة',
            'سبب الخطأ' // This will be the error_message column we added
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'FF0000'] // Red background for headers
                ]
            ],
        ];
    }
}
