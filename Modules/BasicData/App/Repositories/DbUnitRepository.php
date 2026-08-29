<?php

namespace Modules\BasicData\App\Repositories;

use App\Models\BasicDataApp\Unit;

class DbUnitRepository extends BasicDataBaseRepository
{
    protected array $fieldSearchable = ['name', 'status'];
    protected ?string $modelTranslation = 'basicdata::models/db_units.singular';

    public function model(): string
    {
        return Unit::class;
    }

    public function header(): array
    {
        return [
            __('basicdata::models/db_units.fields.id'),
            __('basicdata::models/db_units.fields.name'),
            __('basicdata::models/db_units.fields.conversion_factor'),
            __('basicdata::models/db_units.fields.status'),
            __('basicdata::models/db_units.fields.created_at'),
        ];
    }

    public function dataExel(): array
    {
        return Unit::with('translations')
            ->get()
            ->map(function ($unit) {
                return [
                    'id' => $unit->id,
                    'name' => $unit->name,
                    'conversion_factor' => $unit->conversion_factor,
                    'status' => $unit->status_text,
                    'created_at' => $unit->created_at?->format('Y-m-d'),
                ];
            })
            ->toArray();
    }
}
