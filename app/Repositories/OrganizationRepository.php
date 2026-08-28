<?php

namespace App\Repositories;

use App\Models\Branch;
use App\Models\Organization;
use Modules\HR\App\Models\HrAssetType;
use App\Repositories\BaseRepository;

class OrganizationRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'vatNo',
        'CR',
        'logo',
        'signature',
        'status',
        'opening_balance',
        'available_balance',
        'activity',
        'isNew',
        'isPaid',
        'sectionID',
        'PayGateStatus',
        'packageID',
        'Insbpnmbr',
        'Chamber',
        'Nofacility',
        'Ntionladdress',
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return Organization::class;
    }

    public function statuses(): array
    {
        return Organization::statuses();
    }
}
