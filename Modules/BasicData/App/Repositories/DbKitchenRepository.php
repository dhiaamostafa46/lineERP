<?php

namespace Modules\BasicData\App\Repositories;

use Modules\BasicData\App\Models\DbKitchen;

class DbKitchenRepository extends BasicDataBaseRepository
{
    protected array $fieldSearchable = [
        'orgID',
        'branchID',
        'userID',
        'barcode',
        'status',
    ];
    protected ?string $modelTranslation = 'basicdata::models/db_kitchens.singular';

    public function model(): string
    {
        return DbKitchen::class;
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
}
