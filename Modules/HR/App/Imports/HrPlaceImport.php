<?php

namespace Modules\HR\App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Modules\HR\App\Models\HrPlace;
use Modules\HR\App\Models\HrEmployee;
use Modules\HR\App\Models\HrDepartment;
use App\Models\Branch;
use Carbon\Carbon;

class HrPlaceImport implements ToModel, WithStartRow
{
    public function startRow(): int
    {
        return 2;
    }

    public function model(array $row)
    {
        // row[0]: الاسم
        // row[1]: خط العرض
        // row[2]: خط الطول
        // row[3]: العنوان
        // row[4]: المسافة
        // row[5]: الحالة
        // row[6]: النوع
        // row[7]: الأيام
        // row[8]: الموظفين
        // row[9]: الأقسام
        // row[10]: الفروع
        // row[11]: تاريخ البدء
        // row[12]: تاريخ الانتهاء
        // row[13]: تفعيل نطاق التاريخ

        // 1. Map Status
        $status = 1; // Default Inactive
        $statusText = $row[5] ?? '';
        foreach (HrPlace::statuses() as $key => $val) {
            if ($val == $statusText) {
                $status = $key;
                break;
            }
        }

        // 2. Map Flage
        $flage = 1; // Default All
        $flageText = $row[6] ?? '';
        foreach (HrPlace::flages() as $key => $val) {
            if ($val == $flageText) {
                $flage = $key;
                break;
            }
        }

        // 3. Map Days
        $days = [];
        $daysText = $row[7] ?? '';
        if ($daysText) {
            $daysArray = explode(',', $daysText);
            $weekdays = HrPlace::weekdays();
            foreach ($daysArray as $dayName) {
                $dayName = trim($dayName);
                foreach ($weekdays as $key => $val) {
                    if ($val == $dayName) {
                        $days[] = (string)$key;
                        break;
                    }
                }
            }
        }

        // 4. Map Employees
        $employeeIds = [];
        $employeesText = $row[8] ?? '';
        if ($employeesText) {
            $employeeNames = explode(',', $employeesText);
            foreach ($employeeNames as $name) {
                $name = trim($name);
                $emp = HrEmployee::whereHas('main_employee', function($q) use ($name) {
                    $q->where('username', $name);
                })->first();
                if ($emp) {
                    $employeeIds[] = (string)$emp->id;
                }
            }
        }

        // 5. Map Departments
        $departmentIds = [];
        $departmentsText = $row[9] ?? '';
        if ($departmentsText) {
            $departmentNames = explode(',', $departmentsText);
            foreach ($departmentNames as $name) {
                $name = trim($name);
                $dept = HrDepartment::whereHas('translations', function($q) use ($name) {
                    $q->where('name', $name);
                })->first();
                if ($dept) {
                    $departmentIds[] = (string)$dept->id;
                }
            }
        }

        // 6. Map Branches
        $branchIds = [];
        $branchesText = $row[10] ?? '';
        if ($branchesText) {
            $branchNames = explode(',', $branchesText);
            foreach ($branchNames as $name) {
                $name = trim($name);
                $branch = Branch::whereHas('translations', function($q) use ($name) {
                    $q->where('name', $name);
                })->first();
                if ($branch) {
                    $branchIds[] = (string)$branch->id;
                }
            }
        }

        // 7. Parse Dates
        $startDate = !empty($row[11]) ? $this->transformDate($row[11]) : null;
        $endDate = !empty($row[12]) ? $this->transformDate($row[12]) : null;

        return new HrPlace([
            'name'             => $row[0],
            'lat'              => $row[1],
            'lon'              => $row[2],
            'address'          => $row[3],
            'distance'         => $row[4],
            'status'           => $status,
            'flage'            => $flage,
            'day'              => $days,
            'employee_id'      => $employeeIds,
            'department_id'    => $departmentIds,
            'branch_id'        => $branchIds,
            'start_date'       => $startDate,
            'end_date'         => $endDate,
            'enable_daterange' => ($row[13] ?? '') == 'نعم' ? 1 : 0,
        ]);
    }

    private function transformDate($value)
    {
        if (empty($value)) {
            return null;
        }

        if (is_numeric($value)) {
            try {
                return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value);
            } catch (\Exception $e) {
                // Fallback if numeric parsing fails
            }
        }

        try {
            return Carbon::parse($value);
        } catch (\Exception $e) {
            return null;
        }
    }
}
