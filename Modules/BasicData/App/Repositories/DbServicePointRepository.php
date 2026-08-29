<?php

namespace Modules\BasicData\App\Repositories;

use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Builder;
use Modules\BasicData\App\Models\DbServicePoint;

class DbServicePointRepository extends BaseRepository
{
    protected array $fieldSearchable = [
        'orgID',
        'branchID',
        'userID',
        'code',
        'type',
        'status',
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function allQuery(array $search = [], ?int $skip = null, ?int $limit = null): Builder
    {
        return parent::allQuery($search, $skip, $limit);
    }

    public function model(): string
    {
        return DbServicePoint::class;
    }

    public function statuses(): array
    {
        return DbServicePoint::statuses();
    }

    public function types(): array
    {
        return DbServicePoint::types();
    }

    public function listItems(int $id)
    {
        return DbServicePoint::findOrFail($id);
    }

    public function header(): array
    {
        return [
            __('basicdata::models/db_service_points.fields.id'),
            __('basicdata::models/db_service_points.fields.name'),
            __('basicdata::models/db_service_points.fields.code'),
            __('basicdata::models/db_service_points.fields.type'),
            __('basicdata::models/db_service_points.fields.status'),
            __('basicdata::models/db_service_points.fields.created_at'),
        ];
    }

    public function dataExel(): array
    {
        return DbServicePoint::with('translations')
            ->get()
            ->map(function ($point) {
                return [
                    'id' => $point->id,
                    'name' => $point->name,
                    'code' => $point->code ?? '',
                    'type' => $point->type_text,
                    'status' => $point->status_text,
                    'created_at' => $point->created_at?->format('Y-m-d'),
                ];
            })
            ->toArray();
    }

    public function name(): string
    {
        return __('basicdata::models/db_service_points.singular');
    }
}
