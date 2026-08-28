<?php

namespace Modules\HR\App\Repositories;

use Modules\HR\App\Models\HrDocumentType;
use App\Repositories\BaseRepository;

class HrDocumentTypeRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'name',
        'status'
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return HrDocumentType::class;
    }
    public function statuses(): array
    {
        return HrDocumentType::statuses();
    }
}
