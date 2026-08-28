<?php

namespace App\Repositories\vc;

use App\Models\Branch;
use App\Models\Vehicles\Vehicle;
use App\Repositories\BaseRepository;

class VehiclesRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'status',
        'plate',
        'branch_id',
        'store_id',
        'vehicle_brand_id',
        'vehicle_model_id',
        'year',
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return Vehicle::class;
    }

    public function branches(): array
    {
        return Branch::select('id')
            ->with('translations:branch_id,locale,name')
            ->get()
            ->pluck('name', 'id')
            ->toArray();
    }

    public static function normalizePlate($value)
    {
        // حذف أي شيء غير حروف أو أرقام
        // $value = preg_replace('/[^A-Za-z0-9]/', '', $value);
        // حذف الرموز غير المسموحة
        $value = preg_replace('/[^\p{Arabic}\p{L}\p{N}\s]/u', '', $value);

        // توحيد المسافات
        $value = preg_replace('/\s+/', ' ', trim($value));

        // إضافة مسافة بين الحروف والأرقام إن لم توجد
        $value = preg_replace('/([\p{L}\p{Arabic}])(\d)/u', '$1 $2', $value);
        $value = preg_replace('/(\d)([\p{L}\p{Arabic}])/u', '$1 $2', $value);

        // تحويل الحروف لكبيرة
        // dd($value);
        return strtoupper($value);
    }
    //  public function dataExel(): array
    // {
    //     return Brand::with('translations')
    //         ->get()
    //         ->map(function ($brand) {
    //             return [
    //                 'id' => $brand->id,
    //                 'name' => $brand->name,
    //                 // If your Store has stock-related fields, add them here, e.g. 'stock' => $brand->stock
    //                 'status' => $brand->status_text ?? $brand->status,
    //                 'created_at' => $brand->created_at ? $brand->created_at->format('Y-m-d') : null,
    //             ];
    //         })
    //         ->toArray();
    // }
    // public function header()
    // {
    //     return [
    //         __('models/brands.fields.id') ?? 'ID',
    //         __('models/brands.fields.name') ?? 'Name',
    //         __('models/brands.fields.status') ?? 'Status',
    //         __('models/brands.fields.created_at') ?? 'Created At',
    //     ];
    // }
    //  public function name()
    // {
    //     return __('models/brands.singular') ?? 'brands';
    // }
}
