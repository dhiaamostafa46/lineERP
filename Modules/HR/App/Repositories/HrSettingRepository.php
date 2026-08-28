<?php

namespace Modules\HR\App\Repositories;

use App\Models\User;
use Modules\HR\App\Models\HrSetting;
use App\Repositories\BaseRepository;
use Modules\HR\App\Models\HrEmployee;

class HrSettingRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'delivery_payroll_at',
        'preparing_payroll_at',
        'min_salary',
        'max_off_days',
        'currency'
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return HrSetting::class;
    }


    public function missingFingerprintPolicies()
    {

           return HrSetting::missingFingerprintPolicies();
    }

    public function users(): array
    {
        return User::get()->pluck('name', 'id')->toArray();
    }
}
