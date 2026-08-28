<?php

namespace Modules\HR\App\Repositories;

use Modules\HR\App\Models\HrTerminationTypeReward;
use App\Repositories\BaseRepository;

class HrTerminationTypeRewardRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'termination_type_id',
        'percentage',
        'worked_days',
        'fixed_amount'
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return HrTerminationTypeReward::class;
    }
}
