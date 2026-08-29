<?php

namespace Modules\BasicData\App\Repositories;

use App\Models\BasicDataApp\Unit;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Builder;

class DbUnitRepository extends BaseRepository
{
    protected array $fieldSearchable = ['name', 'status'];

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
        return Unit::class;
    }

    public function statuses(): array
    {
        return Unit::statuses();
    }

    public function listItems(int $id)
    {
        return Unit::findOrFail($id);
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

    public function name(): string
    {
        return __('basicdata::models/db_units.singular');
    }
}
