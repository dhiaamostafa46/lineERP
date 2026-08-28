<?php

namespace App\Repositories;

use App\Models\Project;

class ProjectRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'name',
        'code',
        'status',
        'company_id',
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return Project::class;
    }

    public function statuses(): array
    {
        return Project::statuses();
    }
}
