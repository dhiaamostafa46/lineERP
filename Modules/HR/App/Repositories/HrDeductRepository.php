<?php

namespace Modules\HR\App\Repositories;

use Modules\HR\App\Models\HrDeduct;
use App\Repositories\BaseRepository;

class HrDeductRepository extends BaseRepository
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
        return HrDeduct::class;
    }

    public function statuses(): array
    {
        return HrDeduct::statuses();
    }
}
