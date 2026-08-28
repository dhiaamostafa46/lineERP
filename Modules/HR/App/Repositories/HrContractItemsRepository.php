<?php

namespace Modules\HR\App\Repositories;

use Modules\HR\App\Models\HrContractType;
use App\Repositories\BaseRepository;
use Modules\HR\App\Models\HrContract;
use Modules\HR\App\Models\HrContractitem;

class HrContractItemsRepository extends BaseRepository
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
        return HrContractitem::class;
    }

    public function statuses(): array
    {
        return HrContractitem::statuses();
    }

    public function listItems($id)
    {
        return HrContract::findOrFail($id);
    }
}
