<?php

namespace Modules\BasicData\App\Repositories;
use App\Models\BasicDataApp\Product;
use App\Models\BasicDataApp\Unit;
use App\Repositories\BaseRepository;

class DbUnitRepository extends BaseRepository
{
    protected $fieldSearchable = ['name', 'status'];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function allQuery(array $search = [], ?int $skip = null, ?int $limit = null): \Illuminate\Database\Eloquent\Builder
    {
        $query = parent::allQuery($search, $skip, $limit);
        $table = $this->model()::newModelInstance()->getTable();
        $modelName = class_basename($this->model());
        $permissionPrefix = 'basicdata.' . str_replace('db_', '', \Illuminate\Support\Str::snake(\Illuminate\Support\Str::plural($modelName)));

        if (auth()->check()) {

        }

        return $query;
    }

    public function model(): string
    {
        return Unit::class;
    }

    public function statuses(): array
    {
        return Unit::statuses();
    }

    public function listItems($id)
    {
        return Unit::findOrFail($id);
    }

    public function header()
    {
        return [__('basicdata::models/db_units.fields.id'),__('basicdata::models/db_units.fields.name'), __('basicdata::models/db_units.fields.conversion_factor'), __('basicdata::models/db_units.fields.status'), __('basicdata::models/db_units.fields.created_at')];
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
                    'created_at' => $unit->created_at->format('Y-m-d'),
                ];
            })
            ->toArray();
    }


    public function name()
    {
        return  __('basicdata::models/db_units.singular');
    }
}
