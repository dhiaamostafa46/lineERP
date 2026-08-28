<?php

namespace App\Repositories;

use App\Models\Hub\HubApp;

class HubAppRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'app_code',
        'name',
        'category',
        'is_active',
        'connection_status',
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return HubApp::class;
    }

    /**
     * Get all activated applications keyed by app_code for a specific organization.
     */
    public function getActivatedAppsForOrg(int $orgId = 1)
    {
        return $this->model->where('org_id', $orgId)->get()->keyBy('app_code');
    }

    /**
     * Find a specific application by app_code for an organization.
     */
    public function findByCode(string $code, int $orgId = 1): ?HubApp
    {
        return $this->model->where('app_code', $code)->where('org_id', $orgId)->first();
    }
}
