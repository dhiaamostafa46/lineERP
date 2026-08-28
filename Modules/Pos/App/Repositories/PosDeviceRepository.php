<?php

namespace Modules\Pos\App\Repositories;

use Modules\Pos\App\Models\PosDevice;
use App\Repositories\BaseRepository;

class PosDeviceRepository extends BaseRepository
{
    protected $fieldSearchable = ['name', 'store_id', 'branch_id', 'is_active'];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function allQuery(array $search = [], ?int $skip = null, ?int $limit = null): \Illuminate\Database\Eloquent\Builder
    {
        $query = parent::allQuery($search, $skip, $limit);
        
        // Include relations for index table
        $query->with(['store', 'branch']);

        return $query->orderBy('id', 'desc');
    }

    public function model(): string
    {
        return PosDevice::class;
    }

    public function header(): array
    {
        return [
            __('pos::models/devices.fields.id'),
            __('pos::models/devices.fields.name'),
            __('pos::models/devices.fields.store_id'),
            __('pos::models/devices.fields.branch_id'),
            __('pos::models/devices.fields.is_active'),
            __('pos::models/devices.fields.created_at'),
        ];
    }

    public function dataExel(): array
    {
        return PosDevice::with(['store', 'branch'])
            ->get()
            ->map(function ($device) {
                return [
                    'id' => $device->id,
                    'name' => $device->name,
                    'store_id' => $device->store ? $device->store->name : '',
                    'branch_id' => $device->branch ? $device->branch->name : '',
                    'is_active' => $device->is_active_text,
                    'created_at' => $device->created_at->format('Y-m-d'),
                ];
            })
            ->toArray();
    }

    public function name()
    {
        return __('pos::models/devices.singular');
    }
}
