<?php

namespace App\Repositories\vc;


use App\Models\Vehicles\Brand;
use App\Repositories\BaseRepository;

class BrandRepository extends BaseRepository
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
        return Brand::class;
    }

    public function statuses(): array
    {
        return Brand::statuses();
    }
     public function dataExel(): array
    {
        return Brand::with('translations')
            ->get()
            ->map(function ($brand) {
                return [
                    'id' => $brand->id,
                    'name' => $brand->name,
                    // If your Store has stock-related fields, add them here, e.g. 'stock' => $brand->stock
                    'status' => $brand->status_text ?? $brand->status,
                    'created_at' => $brand->created_at ? $brand->created_at->format('Y-m-d') : null,
                ];
            })
            ->toArray();
    }
    public function header()
    {
        return [
            __('models/brands.fields.id') ?? 'ID',
            __('models/brands.fields.name') ?? 'Name',
            __('models/brands.fields.status') ?? 'Status',
            __('models/brands.fields.created_at') ?? 'Created At',
        ];
    }
     public function name()
    {
        return __('models/brands.singular') ?? 'brands';
    }
}
