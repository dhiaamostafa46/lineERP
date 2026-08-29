<?php

namespace Modules\BasicData\App\Repositories;

use Modules\BasicData\App\Models\DbServicePoint;

class DbServicePointRepository extends BasicDataBaseRepository
{
    protected array $fieldSearchable = [
        'orgID',
        'branchID',
        'userID',
        'code',
        'type',
        'status',
    ];
    protected ?string $modelTranslation = 'basicdata::models/db_service_points.singular';

    public function model(): string
    {
        return DbServicePoint::class;
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
}
