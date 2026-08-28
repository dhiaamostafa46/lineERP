<?php

namespace Modules\HR\App\Repositories;

use Modules\HR\App\Models\HrTrackingApproval;
use App\Repositories\BaseRepository;

class HrTrackingApprovalRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'trackable',
        'user_id',
        'sort',
        'is_current'
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return HrTrackingApproval::class;
    }
}
