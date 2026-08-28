<?php

namespace Modules\HR\App\Repositories;

use Modules\HR\App\Models\HrTermination;
use App\Repositories\BaseRepository;

class HrTerminationRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'termination_type_id',
        'employee_id',
        'worked_days',
        'last_reward'
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return HrTermination::class;
    }
}
