<?php

namespace Modules\HR\App\Repositories;


use App\Repositories\BaseRepository;
use Modules\HR\App\Models\HrEmployee;
use Modules\HR\App\Models\HrGroup;
use Modules\HR\App\Models\HrGroupDetail;

class HrGroupDetailRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'name',
        'hr_group_id',
        'employee_id'
    ];


  
    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return HrGroupDetail::class;
    }


    public function statuses(): array
    {
        return HrGroupDetail::statuses();
    }

    public function employees(): array
    {
        return HrEmployee::with('main_employee:id,username')->get()->pluck('username', 'id')->toArray();
    }
}
