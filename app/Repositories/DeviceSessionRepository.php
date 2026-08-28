<?php

namespace App\Repositories;

use App\Models\Branch;
use App\Models\DeviceSession;
use App\Models\User;
use Modules\HR\App\Models\HrAssetType;
use App\Repositories\BaseRepository;

class DeviceSessionRepository extends BaseRepository
{
    protected $fieldSearchable = [
         'user_id',
        'device_token',
        'device_serial',
        'device_name',
        'user_agent',
        'device_ip',
        'ip',
        'device_type',
        'browser',
        'os',
        'is_active',
        'last_activity_at',
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return DeviceSession::class;
    }

    public function statuses(): array
    {
        return DeviceSession::statuses();
    }


    public function users ()
    {

        return User::get()->pluck('name', 'id')->toArray();


    }
}
