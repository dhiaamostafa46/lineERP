<?php

namespace App\Repositories;

use App\Models\Branch;
use Modules\HR\App\Models\HrAssetType;
use App\Repositories\BaseRepository;

class BranchRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'user_id',
        'phone',
        'area',
        'city',
        'district',
        'long',
        'lat',
        'distance',
        'manager',
        'description',
        'status',
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return Branch::class;
    }

    public function statuses(): array
    {
        return Branch::statuses();
    }
}
