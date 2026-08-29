<?php

namespace Modules\BasicData\App\Repositories;

use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Builder;
use Modules\BasicData\App\Models\DbKitchen;

class DbKitchenRepository extends BaseRepository
{
    protected array $fieldSearchable = [
        'orgID',
        'branchID',
        'userID',
        'barcode',
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
        return DbKitchen::class;
    }

    public function statuses(): array
    {
        return DbKitchen::statuses();
    }

    public function listItems(int $id)
    {
        return DbKitchen::findOrFail($id);
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
                    'created_at' => $kitchen->created_at?->format('Y-m-d'),
                ];
            })
            ->toArray();
    }

    public function name(): string
    {
        return __('basicdata::models/db_kitchens.singular');
    }
}
