<?php

namespace Modules\HR\App\Repositories;

use Modules\HR\App\Models\HrHolidayType;
use App\Repositories\BaseRepository;

class HrHolidayTypeRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'name',
        'status',
        'off_days',
        'type'
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return HrHolidayType::class;
    }

    public function statuses(): array
    {
        return HrHolidayType::statuses();
    }
    public function types(): array
    {
        return HrHolidayType::types();
    }
}
