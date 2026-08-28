<?php

namespace Modules\HR\App\Repositories;

use App\Models\User;
use Modules\HR\App\Models\HrPayrollApproval;
use App\Repositories\BaseRepository;

class HrPayrollApprovalRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'payroll_id',
        'user_id',
        'status'
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return HrPayrollApproval::class;
    }

    public function users($without_users): array
    {
        return User::whereNotIn('id', $without_users)->get()->pluck('name', 'id')->toArray();
    }
}
