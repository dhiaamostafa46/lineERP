<?php

namespace Modules\HR\App\Repositories;

use Modules\HR\App\Models\HrJob;
use App\Repositories\BaseRepository;

class HrJobRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'name',
        'member',
        'employee_id'
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return HrJob::class;
    }

    public function licenses(): array
    {
        return HrJob::licenses();
    }

    public function statuses(): array
    {
        return HrJob::statuses();
    }
}
