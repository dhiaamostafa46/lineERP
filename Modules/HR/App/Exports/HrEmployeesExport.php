<?php


namespace Modules\HR\App\Exports;

use App\Models\Pilgrim;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class HrEmployeesExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    public $employees;

    public function __construct($employees)
    {
        $this->employees = $employees;
    }

    public function collection()
    {
        return $this->employees;
    }

    public function headings(): array
    {
        return [
            'الاسم الكامل',
            'اسم المستخدم',
            'رقم الجوال',
            'البريد الإلكتروني (الشركة)',
            'البريد الإلكتروني (الشخصي)',
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
            'الحد الأقصى للإجازات',
            'رصيد الإجازات السابق',
            'الحد الأقصى للسلف',
            'المستوى الوظيفي',
            'التخصص',
            'الجنس',
            'الحالة الاجتماعية',
            'رقم الهوية/الإقامة',
            'تاريخ انتهاء الهوية',
            'رقم التأمين',
            'تاريخ انتهاء التأمين',
            'تاريخ انتهاء الرخصة',
        ];
    }

    public function map($employee): array
    {
        return [
            $employee->main_employee->full_name ?? '',
            $employee->main_employee->username ?? '',
            $employee->user->phone ?? '',
            $employee->user->email ?? '',
            $employee->main_employee->email ?? '',
            $employee->job_number ?? '',
            $employee->department->name ?? '',
            $employee->job->name ?? '',
            $employee->shift->name ?? '',
            $employee->main_employee->dob ?? '',
            $employee->start_at ?? '',
            $employee->main_employee->bank->iban ?? '',
            $employee->main_employee->branch->name ?? '',
            $employee->main_employee->address ?? '',
            $employee->main_employee->national_address ?? '',
            $employee->main_employee->religion ?? '',
            $employee->main_employee->nationality ?? '',
            $employee->max_off_days ?? 0,
            $employee->vacation_balance ?? 0,
            $employee->max_advance ?? 0,
            $employee->job_level ?? '',
            $employee->specialty ?? '',
            $employee->main_employee->gender_text ?? '',
            $employee->main_employee->marital_status_text ?? '',
            $employee->main_employee->identity->identity_no ?? '',
            $employee->main_employee->identity->identity_expired_at ?? '',
            $employee->main_employee->identity->insurance_no ?? '',
            $employee->main_employee->identity->insurance_expired_at ?? '',
            $employee->license_expired_at ?? '',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text.
            1    => ['font' => ['bold' => true]],
            // Style the first column as bold text.
            'A'    => ['font' => ['bold' => true]],
        ];
    }
}
