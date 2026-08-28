<?php

namespace Modules\Invoices\App\Repositories;

use App\Models\invApp\InvSupplier;
use App\Repositories\BaseRepository;

class InvSupplierRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'phone', 'email', 'vat_number', 'cr_number', 'address', 'country', 'city', 'district', 'street', 'building_number', 'postal_code', 'additional_number', 'tree_account_id', 'branch_id', 'credit_limit', 'status',
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return InvSupplier::class;
    }

    public function allQuery(array $search = [], ?int $skip = null, ?int $limit = null): \Illuminate\Database\Eloquent\Builder
    {
        $query = parent::allQuery($search, $skip, $limit);

        if (auth()->check()) {
            $table = $this->model()::newModelInstance()->getTable();
            $permissionPrefix = 'invoices.suppliers';


        }

        return $query;
    }

    /**
     * البحث والفلترة مع دعم الترقيم الصفحي
     */
    public function getHeaders(): array
    {
        return [
            __('invoices::models/inv_suppliers.fields.id'),
            __('invoices::models/inv_suppliers.fields.name'),
            __('invoices::models/inv_suppliers.fields.phone'),
            __('invoices::models/inv_suppliers.fields.email'),
            __('invoices::models/inv_suppliers.fields.vat_number'),
            // __('invoices::models/inv_suppliers.fields.cr_number'),
            __('invoices::models/inv_suppliers.fields.country'),
            __('invoices::models/inv_suppliers.fields.city'),
            // __('invoices::models/inv_suppliers.fields.district'),
            // __('invoices::models/inv_suppliers.fields.street'),
            // __('invoices::models/inv_suppliers.fields.building_number'),
            // __('invoices::models/inv_suppliers.fields.postal_code'),
            // __('invoices::models/inv_suppliers.fields.additional_number'),
            // __('invoices::models/inv_suppliers.fields.tree_account_id'),
            __('invoices::models/inv_suppliers.fields.credit_limit'),
            __('invoices::models/inv_suppliers.fields.status'),
            __('invoices::models/inv_suppliers.fields.created_at'),
        ];
    }

    public function dataExcel(): array
    {
        return InvSupplier::with(['translations', 'treeAccount'])
            ->get()
            ->map(function ($supplier) {
                return [
                    'id' => $supplier->id,
                    'name' => $supplier->name,
                    'phone' => $supplier->phone,
                    'email' => $supplier->email,
                    'vat_number' => $supplier->vat_number,
                    // 'cr_number'         => $supplier->cr_number,
                    'country' => $supplier->country,
                    'city' => $supplier->city,
                    // 'district'          => $supplier->district,
                    // 'street'            => $supplier->street,
                    // 'building_number'   => $supplier->building_number,
                    // 'postal_code'       => $supplier->postal_code,
                    // 'additional_number' => $supplier->additional_number,
                    // 'tree_account'      => $supplier->treeAccount?->name ?? '---',
                    'credit_limit' => number_format($supplier->credit_limit, 2),
                    'status' => $supplier->status_text,
                    'created_at' => $supplier->created_at ? $supplier->created_at->format('Y-m-d') : '---',
                ];
            })
            ->toArray();
    }

    public function name()
    {
        return __('invoices::models/inv_suppliers.plural');
    }

    public function create(array $input, bool $withLog = true): \Illuminate\Database\Eloquent\Model
    {
        $input['org_id'] = auth()->user()->org_id ?? null;
        if (isset($input['store_id'])) {
            $input['branch_id'] = \App\Models\StoreApp\Store::findOrFail($input['store_id'])->branch_id;
        }

        return parent::create($input, $withLog);
    }

    public function update(array $input, int $id, bool $withLog = true)
    {
        $input['org_id'] = auth()->user()->org_id ?? null;
        if (isset($input['store_id'])) {
            $input['branch_id'] = \App\Models\StoreApp\Store::findOrFail($input['store_id'])->branch_id;
        }

        return parent::update($input, $id, $withLog);
    }
}
