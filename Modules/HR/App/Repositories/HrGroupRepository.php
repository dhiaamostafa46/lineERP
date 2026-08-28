<?php

namespace Modules\HR\App\Repositories;

use App\Repositories\BaseRepository;
use Modules\HR\App\Models\HrEmployee;
use Modules\HR\App\Models\HrGroup;
use Modules\HR\App\Models\HrGroupDetail;

class HrGroupRepository extends BaseRepository
{
    protected $fieldSearchable = ['name', 'status', 'license_required'];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return HrGroup::class;
    }

    // public function CreateMember($data)
    // {
    //     //     if(count($data->group_details)>0)
    //     //     {
    //     // dd("dddddddddddddd");
    //     //     }
    //     dd($data);
    // }

    public function createMember(array $data, $hrGroup)
    {
        // Begin transaction for atomic operation


        // try {
        //     // Delete existing group details
             $hrGroup->details()->delete();  // Correct method to delete related records

            // Check if group details are provided and is an array
            if (isset($data['group_details']) && is_array($data['group_details'])) {
                foreach ($data['group_details'] as $detail) {
                    if (!empty($detail['employee_id'])) {
                        // Create new group detail record
                        HrGroupDetail::create([
                            'hr_group_id' => $hrGroup->id,
                            'employee_id' => $detail['employee_id'],
                        ]);
                    }
                }
            }

          
    }


    public function statuses(): array
    {
        return HrGroup::statuses();
    }

    public function employees(): array
    {
        return HrEmployee::with('main_employee:id,username')->get()->pluck('username', 'id')->toArray();
    }
}
