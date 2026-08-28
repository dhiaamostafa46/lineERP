<?php

namespace Modules\HR\App\Repositories;

use Modules\HR\App\Models\HrCalendarEvents;
use App\Repositories\BaseRepository;

class HrCalendarEventRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'name',
        'start_date',
        'end_date',
        'status',
        'type'
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return HrCalendarEvents::class;
    }

    public function statuses()
    {
        return HrCalendarEvents::statuses();
    }

    public function types()
    {
        return HrCalendarEvents::types();
    }
}
