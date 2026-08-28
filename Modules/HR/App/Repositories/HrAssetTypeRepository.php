<?php

namespace Modules\HR\App\Repositories;

use Modules\HR\App\Models\HrAssetType;
use App\Repositories\BaseRepository;

class HrAssetTypeRepository extends BaseRepository
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
        return HrAssetType::class;
    }

    public function statuses(): array
    {
        return HrAssetType::statuses();
    }
}
