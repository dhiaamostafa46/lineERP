<?php

namespace Modules\HR\App\Repositories;

use Modules\HR\App\Models\HrTerminationType;
use App\Repositories\BaseRepository;

class HrTerminationTypeRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'name',
        'status'
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return HrTerminationType::class;
    }

    public function statuses(): array
    {
        return HrTerminationType::statuses();
    }
}
