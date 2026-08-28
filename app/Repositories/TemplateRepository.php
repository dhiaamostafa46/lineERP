<?php

namespace App\Repositories;

use App\Models\Template;
use App\Repositories\BaseRepository;

class TemplateRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'org_id',
        'branch_id',
        'document_type',
        'print_format',
        'status',
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return Template::class;
    }

    public function statuses(): array
    {
        return Template::statuses();
    }
}
