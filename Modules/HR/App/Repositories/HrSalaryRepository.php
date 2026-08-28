<?php

namespace Modules\HR\App\Repositories;

use Modules\HR\App\Models\HrDeduct;
use Modules\HR\App\Models\HrSalary;
use App\Repositories\BaseRepository;
use Modules\HR\App\Models\HrEmployee;
use Modules\HR\App\Models\HrAllowance;
use Modules\HR\App\Models\HrSalaryAllowance;
use Modules\HR\App\Models\HrSalaryDeduct;

class HrSalaryRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'employee_id',
        'basic'
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return HrSalary::class;
    }

    public function employees(): array
    {
        return HrEmployee::doesntHave('salary')->get()->pluck('username', 'id')->toArray();
    }

    public function filter_employees(): array
    {
        return HrEmployee::get()->pluck('username', 'id')->toArray();
    }

    public function allowances(): object
    {
        return HrAllowance::activeOnly()->get();
    }

    public function deducts(): object
    {
        return HrDeduct::activeOnly()->get();
    }

    public function create_allowances($allowances, $salary_id): void
    {
        foreach ($allowances as $key => $value) {
            HrSalaryAllowance::updateOrCreate(['allowance_id' => $key, 'salary_id' => $salary_id], ['amount' => $value]);
        }
    }

    public function create_deducts($deducts, $salary_id): void
    {
        foreach ($deducts as $key => $value) {
            HrSalaryDeduct::updateOrCreate(['deduct_id' => $key, 'salary_id' => $salary_id], ['amount' => $value]);
        }
    }
}
