<?php

namespace App\Imports;

use App\Models\Employee;
use Maatwebsite\Excel\Concerns\ToModel;
use App\Helpers\OptimizeImportEmployeeTrait;
use Modules\HR\App\Models\HrEmployee;
use Modules\HR\App\Models\HrSalary;

class EmployeesImport implements ToModel
{
    use OptimizeImportEmployeeTrait;

    public function __construct()
    {
    }

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $main_employee = Employee::updateOrCreate(
            [
                'username' => $row[1],
                'phone'    => $row[2],
                'email'    => $row[3],
            ],
            [
                'full_name'          => $row[0],
                'username'           => $row[1],
                'phone'              => $row[2],
                'email'              => $row[3],
                'dob'                => $row[4],
                'address'            => $row[5],
                'national_address'   => $row[6],
                'religion'           => $row[7],
                'nationality'        => $row[8],
            ]
        );

        $employee = HrEmployee::updateOrCreate([
            'employee_id' => $main_employee->id,
        ], [
            'max_off_days'  => $row[9],
            'max_advance'   => $row[10],
            'job_level'     => $row[11],
            'specialty'     => $row[12],
        ]);

        HrSalary::updateOrCreate([
            'employee_id' => $employee->id,
        ], [
            'basic'    => config('statusSystem.minimum_basic_salary') ?? 0,
        ]);
        $main_employee->identity()->create([]);
        $main_employee->bank()->create([]);
    }
}
