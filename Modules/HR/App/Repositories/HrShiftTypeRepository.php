<?php

namespace Modules\HR\App\Repositories;

use App\Repositories\BaseRepository;
use Modules\HR\App\Models\HrShiftType;

class HrShiftTypeRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'name',
        'status',
        'type',
        'work_hours',
        'from',
        'to'
    ];



    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return HrShiftType::class;
    }

    public function types(): array
    {
        return HrShiftType::types();
    }

    public function statuses(): array
    {
        return HrShiftType::statuses();
    }

    public function create_shifts($shifts, $shift_type)
    {
        if ($shift_type->type == HrShiftType::TYPE_STATIC) {
            foreach ($shifts as $shift) {
                $shift_type->shifts()->create([
                    'from' => $shift['from'],
                    'to' => $shift['to'],
                    'is_active' => isset($shift['is_active']),
                ]);
            }
        }
    }
    public function update_shifts($shifts, $shift_type)
    {

        
        $shifts_updated = [];
        if ($shift_type->type == HrShiftType::TYPE_STATIC || $shift_type->type == HrShiftType::TYPE_SPECIFIC ) {
            foreach ($shifts as $shift) {
                if (!isset($shift['shift_id'])) {
                    $shifts_updated[] = $shift_type->shifts()->create([
                        'from' => $shift['from'],
                        'to' => $shift['to'],
                        'is_active' => isset($shift['is_active']),
                    ])->id;
                } else {
                    $shifts_updated[] = $shift['shift_id'];
                    $shift_type->shifts()
                        ->where('id', $shift['shift_id'])
                        ->update([
                            'from' => $shift['from'],
                            'to' => $shift['to'],
                            'is_active' => isset($shift['is_active']),
                        ]);
                }
            }
            $shift_type->shifts()->whereNotIn('id', $shifts_updated)->delete();
        } else {
            $shift_type->shifts()->delete();
        }
    }
}
