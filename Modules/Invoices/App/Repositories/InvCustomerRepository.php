<?php

namespace Modules\Invoices\App\Repositories;

use App\Models\invApp\InvCustomer;
use App\Repositories\BaseRepository;

class InvCustomerRepository extends BaseRepository
{
    protected $fieldSearchable = ['phone', 'email', 'vat_number', 'cr_number', 'country', 'city', 'district', 'street', 'building_number', 'postal_code', 'additional_number', 'tree_account_id', 'status', 'file'];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return InvCustomer::class;
    }

    public function allQuery(array $search = [], ?int $skip = null, ?int $limit = null): \Illuminate\Database\Eloquent\Builder
    {
        $query = parent::allQuery($search, $skip, $limit);

        if (auth()->check()) {
            $table = $this->model()::newModelInstance()->getTable();
            $permissionPrefix = 'invoices.customers';


        }

        return $query;
    }

    /**
     * Search and Filter customers with pagination support
     */
    public function getHeaders(): array
    {
        return [
            __('invoices::models/inv_customers.fields.id'),
            __('invoices::models/inv_customers.fields.name'),
            __('invoices::models/inv_customers.fields.phone'),
            __('invoices::models/inv_customers.fields.email'),
            __('invoices::models/inv_customers.fields.vat_number'),
            // __('invoices::models/inv_customers.fields.cr_number'),
            __('invoices::models/inv_customers.fields.country'),
            __('invoices::models/inv_customers.fields.city'),
            // __('invoices::models/inv_customers.fields.district'),
            // __('invoices::models/inv_customers.fields.street'),
            // __('invoices::models/inv_customers.fields.building_number'),
            // __('invoices::models/inv_customers.fields.postal_code'),
            // __('invoices::models/inv_customers.fields.additional_number'),
            // __('invoices::models/inv_customers.fields.tree_account_id'),
            __('invoices::models/inv_customers.fields.credit_limit'),
            __('invoices::models/inv_customers.fields.status'),
            __('invoices::models/inv_customers.fields.created_at'),
        ];
    }

    public function dataExcel(): array
    {
        return InvCustomer::with(['translations', 'treeAccount'])
            ->get()
            ->map(function ($customer) {
                return [
                    'id' => $customer->id,
                    'name' => $customer->name,
                    'phone' => $customer->phone,
                    'email' => $customer->email,
                    'vat_number' => $customer->vat_number,
                    // 'cr_number'         => $customer->cr_number,
                    'country' => $customer->country,
                    'city' => $customer->city,
                    // 'district'          => $customer->district,
                    // 'street'            => $customer->street,
                    // 'building_number'   => $customer->building_number,
                    // 'postal_code'       => $customer->postal_code,
                    // 'additional_number' => $customer->additional_number,
                    // 'tree_account'      => $customer->treeAccount?->name ?? '---',
                    'credit_limit' => number_format($customer->credit_limit, 2),
                    'status' => $customer->status_text,
                    'created_at' => $customer->created_at ? $customer->created_at->format('Y-m-d') : '---',
                ];
            })
            ->toArray();
    }

    public function name()
    {
        return __('invoices::models/inv_customers.plural');
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
