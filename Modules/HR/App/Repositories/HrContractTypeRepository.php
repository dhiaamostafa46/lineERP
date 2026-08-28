<?php

namespace Modules\HR\App\Repositories;

use Modules\HR\App\Models\HrContractType;
use App\Repositories\BaseRepository;

class HrContractTypeRepository extends BaseRepository
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
        return HrContractType::class;
    }
    
    public function statuses(): array
    {
        return HrContractType::statuses();
    }
}
