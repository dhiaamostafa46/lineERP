<?php

namespace Modules\BasicData\App\Repositories;
use App\Models\BasicDataApp\Product;
use App\Models\BasicDataApp\Unit;
use App\Repositories\BaseRepository;
use Modules\BasicData\App\Models\DbKitchen;
use Modules\BasicData\App\Models\DbServicePoint;

class DbKitchenRepository extends BaseRepository
{
    protected $fieldSearchable = [
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
        return  DbKitchen::class;
    }

    public function statuses(): array
    {
        return DbKitchen::statuses();
    }

    public function listItems($id)
    {
        return  DbKitchen::findOrFail($id);
    }


  public function header(): array
{
    return [
         __('basicdata::models/db_kitchens.fields.id'),
        __('basicdata::models/db_kitchens.fields.name'),
        __('basicdata::models/db_kitchens.fields.barcode'),
        __('basicdata::models/db_kitchens.fields.status'),
        __('basicdata::models/db_kitchens.fields.created_at'),

    ];
}

public function dataExel(): array
{
    return DbKitchen::with('translations')
        ->get()
        ->map(function ($kitchen) {
            return [
                'id' => $kitchen->id,
                'name' => $kitchen->name,
                'barcode' => $kitchen->barcode ?? '',
                'status' => $kitchen->status_text,
                'created_at' => $kitchen->created_at->format('Y-m-d'),

            ];
        })
        ->toArray();
}

    public function name()
    {
        return __('basicdata::models/db_kitchens.singular');
    }
}
