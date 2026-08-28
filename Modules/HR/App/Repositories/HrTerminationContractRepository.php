<?php

namespace Modules\HR\App\Repositories;

use Modules\HR\App\Models\HrTerminationContract;
use App\Repositories\BaseRepository;

class HrTerminationContractRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'termination_id',
        'contract_id',
        'worked_days'
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return HrTerminationContract::class;
    }
}
