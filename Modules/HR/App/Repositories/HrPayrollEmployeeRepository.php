<?php

namespace Modules\HR\App\Repositories;

use App\Repositories\BaseRepository;
use Modules\HR\App\Helpers\PayrollTrait;
use Modules\HR\App\Models\HrPayrollEmployee;

class HrPayrollEmployeeRepository extends BaseRepository
{
    use PayrollTrait;

    protected $fieldSearchable = [
        'employee_id',
        'payroll_id',
        'salary_id',
        'total_allowances',
        'total_deducts',
        'basic_salary',
        'status'
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return HrPayrollEmployee::class;
    }

    public function updateOrCreate(array $inputs = [], int $payroll_id): HrPayrollEmployee
    {
        $inputs['payroll_id'] = $payroll_id;
        return HrPayrollEmployee::updateOrCreate([
            'payroll_id' => $payroll_id,
            'employee_id' => $inputs['employee_id']
        ], $inputs);
    }

    public function updateOrCreateMany(object|array $attributes = [], int $payroll_id): array
    {
        $result = [];
        foreach ($attributes as $attribute) {
            $result[] = $this->updateOrCreate((array)$attribute, $payroll_id);
        }
        return $result;
        
    }
}
