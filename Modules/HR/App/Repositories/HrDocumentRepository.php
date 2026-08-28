<?php

namespace Modules\HR\App\Repositories;

use Modules\HR\App\Models\HrEmployee;
use App\Repositories\BaseRepository;
use Modules\HR\App\Models\HrDocument;
use Modules\HR\App\Models\HrDocumentType;

class HrDocumentRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'employee_id',
        'type_id',
        'file',
        'status'
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return HrDocument::class;
    }

    public function statuses(): array
    {
        return HrDocument::statuses();
    }

    public function types(): array
    {
        return HrDocumentType::activeOnly()->get()->pluck('name', 'id')->toArray();
    }

    public function employees(): array
    {
        return HrEmployee::with('main_employee:id,username')->get()->pluck('username', 'id')->toArray();
    }
}
