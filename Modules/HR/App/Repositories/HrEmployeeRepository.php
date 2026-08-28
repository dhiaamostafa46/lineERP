<?php

namespace Modules\HR\App\Repositories;

use App\Models\Branch;
use App\Models\Role;
use App\Models\User;
use App\Models\Employee;
use App\Models\EmployeeIdentity;
use Modules\HR\App\Models\HrJob;
use Modules\HR\App\Models\HrSalary;
use App\Repositories\BaseRepository;
use Modules\HR\App\Helpers\RemoveEmployee;
use Modules\HR\App\Models\HrEmployee;
use Modules\HR\App\Models\HrShiftType;
use Modules\HR\App\Models\HrDepartment;

class HrEmployeeRepository extends BaseRepository
{
    use RemoveEmployee;
    protected $fieldSearchable = ['employee_id', 'job_id', 'department_id', 'shift_id', 'max_off_days', 'max_advance', 'job_level', 'specialty', 'start_at'];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return HrEmployee::class;
    }

    public function employees(): array
    {
        return Employee::select('username', 'id')->get()->pluck('username', 'id')->toArray();
    }

    public function jobs(): array
    {
        return HrJob::select('id', 'status')->with('translations:hr_job_id,locale,name')->activeOnly()->get()->pluck('name', 'id')->toArray();
    }

    public function departments(): array
    {
        return HrDepartment::select('id')->with('translations:hr_department_id,locale,name')->activeOnly()->get()->pluck('name', 'id')->toArray();
    }

    public function shifts(): array
    {
        return HrShiftType::select('id')->with('translations:hr_shift_type_id,locale,name')->activeOnly()->get()->pluck('name', 'id')->toArray();
    }
    public function branchs(): array
    {
        return Branch::select('id', 'status')->with('translations:branch_id,locale,name')->activeOnly()->get()->pluck('name', 'id')->toArray();
    }

    public function genders(): array
    {
        return Employee::genders();
    }

    public function maritalStatuses(): array
    {
        return Employee::maritalStatuses();
    }

    public function fingerprintExempts(): array
    {
        return HrEmployee::fingerprintExempts();
    }

    public function AttendanceTypes(): array
    {
        return HrEmployee::attendanceTypes();
    }

    public function identityTypes(): array
    {
        return EmployeeIdentity::types();
    }

    public function create_salary($empLoyee_id, $inputdata)
    {
        return HrSalary::updateOrCreate(
            [
                'employee_id' => $empLoyee_id, // Search criteria
            ],
            [
                'basic' => $inputdata['basic'] ?? config('statusSystem.minimum_basic_salary'), // Data to insert/update
            ],
        );
    }

    public function user_roles(): array
    {
        return Role::get()->pluck('name', 'name')->toArray();
    }

    public function user_statuses(): array
    {
        return User::statuses();
    }

    public function ContactModel($employee)
    {
        dd($employee);
    }

    public function DeleteEmp($id)
    {
        return $this->DeleteEmployee($id);
    }

    // public function shift(): array
    // {
    //     return HrShiftType::select('id')
    //         ->with('translations:hr_shift_type_id,locale,name')
    //         ->activeOnly()
    //         ->get()
    //         ->pluck('name', 'id')
    //         ->toArray();
    // }
}
