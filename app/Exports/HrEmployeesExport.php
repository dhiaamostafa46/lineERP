<?php

namespace App\Exports;

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
            __('models/employees.fields.full_name'),
            __('models/employees.fields.username'),
            __('models/employees.fields.phone'),
            __('models/employees.fields.email'),
            __('models/employees.fields.dob'),
            __('models/employees.fields.address'),
            __('models/employees.fields.national_address'),
            __('models/employees.fields.religion'),
            __('models/employees.fields.gender'),
            __('models/employees.fields.marital_status'),
            __('models/employees.fields.number_of_children'),
            __('models/employees.fields.nationality'),
            __('hr::models/hr_employees.fields.job_id'),
            __('hr::models/hr_employees.fields.department_id'),
            __('hr::models/hr_employees.fields.shift_id'),
            __('hr::models/hr_employees.fields.max_off_days'),
            __('hr::models/hr_employees.fields.max_advance'),
            __('hr::models/hr_employees.fields.job_level'),
            __('hr::models/hr_employees.fields.specialty'),
            __('hr::models/hr_employees.fields.start_at'),

        ];
    }

    public function map($employee): array
    {
        return [
            $employee->main_employee->full_name,
            $employee->main_employee->username,
            $employee->main_employee->phone,
            $employee->main_employee->email,
            $employee->main_employee->dob,
            $employee->main_employee->address,
            $employee->main_employee->national_address,
            $employee->main_employee->religion,
            $employee->main_employee->gender_text,
            $employee->main_employee->marital_status_text,
            $employee->main_employee->number_of_children,
            $employee->main_employee->nationality,
            $employee->job->name??'-',
            $employee->department->name??'-',
            $employee->shift->name??'-',
            $employee->max_off_days,
            $employee->max_advance,
            $employee->job_level,
            $employee->specialty,
            $employee->start_at,
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
