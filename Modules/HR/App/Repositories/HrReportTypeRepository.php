<?php

namespace Modules\HR\App\Repositories;

use Modules\HR\App\Models\HrReportType;
use App\Repositories\BaseRepository;

class HrReportTypeRepository extends BaseRepository
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
        return HrReportType::class;
    }

    public function statuses(): array
    {
        return HrReportType::statuses();
    }
}
