<?php

namespace Modules\HR\App\Repositories;

use Modules\HR\App\Models\HrEmployee;
use App\Repositories\BaseRepository;
use Modules\HR\App\Models\HrContract;
use Modules\HR\App\Models\HrContractType;

class HrContractRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'employee_id',
        'type_id',
        'file',
        'status',
        'qiwa_no',
        'start_at',
        'end_at'
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return HrContract::class;
    }
    public function statuses(): array
    {
        return HrContract::statuses();
    }


     public function qiwas(): array
    {
        return HrContract::qiwas();
    }

    public function types(): array
    {
        return HrContractType::activeOnly()->get()->pluck('name', 'id')->toArray();
    }

    public function employees(): array
    {
        return HrEmployee::get()->pluck('username', 'id')->toArray();
    }
}
