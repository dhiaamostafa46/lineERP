<?php

namespace Modules\HR\App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Modules\HR\App\Models\HrPlace;
use Modules\HR\App\Models\HrEmployee;
use Modules\HR\App\Models\HrDepartment;
use App\Models\Branch;

class HrPlaceExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    public $places;

    public function __construct($places)
    {
        $this->places = $places;
    }

    public function collection()
    {
        return $this->places;
    }

    public function headings(): array
    {
        return [
            'الاسم',
            'خط العرض',
            'خط الطول',
            'العنوان',
            'المسافة',
            'الحالة',
            'النوع',
            'الأيام',
            'الموظفين',
            'الأقسام',
            'الفروع',
            'تاريخ البدء',
            'تاريخ الانتهاء',
            'تفعيل نطاق التاريخ'
        ];
    }

    public function map($place): array
    {
        $weekdays = HrPlace::weekdays();
        $flages = HrPlace::flages();
        $statuses = HrPlace::statuses();

        // Map Weekdays
        $dayNames = [];
        if (is_array($place->day)) {
            foreach ($place->day as $dayKey) {
                if (isset($weekdays[$dayKey])) {
                    $dayNames[] = $weekdays[$dayKey];
                }
            }
        }
        $daysString = implode(', ', $dayNames);

        // Map Employees
        $employeeNames = [];
        if (is_array($place->employee_id)) {
            $employeeNames = HrEmployee::whereIn('id', $place->employee_id)
                ->with('main_employee')
                ->get()
                ->pluck('main_employee.username')
                ->toArray();
        }
        $employeesString = implode(', ', $employeeNames);

        // Map Departments
        $departmentNames = [];
        if (is_array($place->department_id)) {
            $departmentNames = HrDepartment::whereIn('id', $place->department_id)
                ->get()
                ->pluck('name')
                ->toArray();
        }
        $departmentsString = implode(', ', $departmentNames);

        // Map Branches
        $branchNames = [];
        if (is_array($place->branch_id)) {
            $branchNames = Branch::whereIn('id', $place->branch_id)
                ->get()
                ->pluck('name')
                ->toArray();
        }
        $branchesString = implode(', ', $branchNames);

        return [
            $place->name,
            $place->lat,
            $place->lon,
            $place->address,
            $place->distance,
            $statuses[$place->status] ?? '',
            $flages[$place->flage] ?? '',
            $daysString,
            $employeesString,
            $departmentsString,
            $branchesString,
            $place->start_date ? $place->start_date->format('Y-m-d H:i') : '',
            $place->end_date ? $place->end_date->format('Y-m-d H:i') : '',
            $place->enable_daterange ? 'نعم' : 'لا',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
