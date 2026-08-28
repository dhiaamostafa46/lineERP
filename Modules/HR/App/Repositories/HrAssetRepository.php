<?php

namespace Modules\HR\App\Repositories;

use Modules\HR\App\Models\HrAsset;
use App\Repositories\BaseRepository;
use Modules\HR\App\Models\HrAssetType;
use Modules\HR\App\Models\HrDepartment;

class HrAssetRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'department_id',
        'type_id',
        'is_new',
        'name',
        'note',
        'status'
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return HrAsset::class;
    }

    // types
    public function types()
    {
        return HrAssetType::activeOnly()->get()->pluck('name', 'id');
    }

    // departments
    public function departments()
    {
        return HrDepartment::activeOnly()->get()->pluck('name', 'id');
    }

    // status array
    public function statuses()
    {
        return $this->model()::statuses();
    }
}
