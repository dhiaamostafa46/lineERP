<?php

namespace Modules\HR\App\Repositories;

use App\Models\Branch;
use App\Repositories\BaseRepository;
use Modules\HR\App\Models\HrAttendancePolicy;
use Modules\HR\App\Models\HrEmployee;
use Modules\HR\App\Models\HrAllowance;
use Modules\HR\App\Models\HrDepartment;
use Modules\HR\App\Models\HrJob;

class HrAttendancePolicyRepository extends BaseRepository
{

    protected $fieldSearchable = [
        'name',
        'description',
        'is_automatic',
        'scope',
        'scope_ids',
        'start_date',
        'end_date',
        'status',
        'type',
        'settings',
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return HrAttendancePolicy::class;
    }


    public function types()
    {
        return HrAttendancePolicy::types();
    }

    public function scopes()
    {
        return HrAttendancePolicy::scopes();
    }

    public function salarys()
    {
        $basicSalary = ['basic' => __('hr::models/hr_salaries.fields.basic')];
        $allowances = HrAllowance::activeOnly()->get()->pluck('name', 'id')->toArray();
    
        return $basicSalary + $allowances;
    }


     public function calculationTypes()
    {
        return HrAttendancePolicy::calculationTypes();
    }

    public function automatics()
    {
        return HrAttendancePolicy::automatics();
    }


    public function statuses()
    {
        return HrAttendancePolicy::statuses();
    }

    public function employees()
    {
        return HrEmployee::get()->pluck('username', 'id')->toArray();
    }



    public function jobs(): array
    {
        return HrJob::select('id', 'status')->with('translations:hr_job_id,locale,name')->activeOnly()->get()->pluck('name', 'id')->toArray();
    }

    public function departments(): array
    {
        return HrDepartment::select('id')->with('translations:hr_department_id,locale,name')->activeOnly()->get()->pluck('name', 'id')->toArray();
    }



    public function Branches()
    {
        return Branch::get()->pluck('name', 'id')->toArray();
    }

}
