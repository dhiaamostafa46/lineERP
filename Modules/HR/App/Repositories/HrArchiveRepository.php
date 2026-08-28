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

class HrArchiveRepository extends BaseRepository
{

    use RemoveEmployee;
    protected $fieldSearchable = [
        'employee_id',
        'job_id',
        'department_id',
        'shift_id',
        'max_off_days',
        'max_advance',
        'job_level',
        'specialty',
        'start_at'
    ];

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
        return HrEmployee::with('main_employee:id,username')->onlyTrashed()->get()->pluck('username', 'id')->toArray();
    }


    public function GetemployeeDeleteShow($id)
    {
        return $this->GetemployeeDelete($id);
    }

    public function jobs(): array
    {
        return HrJob::select('id', 'status')
            ->with('translations:hr_job_id,locale,name')
            ->activeOnly()
            ->get()
            ->pluck('name', 'id')
            ->toArray();
    }

    public function departments(): array
    {
        return HrDepartment::select('id')
            ->with('translations:hr_department_id,locale,name')
            ->activeOnly()
            ->get()
            ->pluck('name', 'id')
            ->toArray();
    }



}
