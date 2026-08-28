<?php

namespace Modules\HR\App\Repositories;

use Modules\HR\App\Models\HrTrackerJob;
use App\Repositories\BaseRepository;

class HrTrackerJobRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'tracker_id',
        'job_id',
        'status'
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return HrTrackerJob::class;
    }
}
