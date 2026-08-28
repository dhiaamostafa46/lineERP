<?php

namespace Modules\Invoices\App\Repositories;

use App\Repositories\BaseRepository;
use Modules\Invoices\App\Models\InvoiceSetting;

class InvSettingRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'sales_prefix',
        'purchase_prefix',
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return InvoiceSetting::class;
    }

    public function getHeaders(): array
    {
        return [
            __('invoices::models/invoices_setting.fields.id'),
            __('invoices::models/invoices_setting.fields.sales_prefix'),
            __('invoices::models/invoices_setting.fields.purchase_prefix'),
            __('invoices::models/invoices_setting.fields.default_vat_rate'),
            __('invoices::models/invoices_setting.fields.created_at'),
        ];
    }

    public function dataExcel(): array
    {
        return InvoiceSetting::get()
            ->map(function ($setting) {
                return [
                    'id' => $setting->id,
                    'sales_prefix' => $setting->sales_prefix,
                    'purchase_prefix' => $setting->purchase_prefix,
                    'default_vat_rate' => $setting->default_vat_rate,
                    'created_at' => $setting->created_at->format('Y-m-d'),
                ];
            })
            ->toArray();
    }

    public function name()
    {
        return __('invoices::models/invoices_setting.singular');
    }

    /**
     * Get first record or new
     */
    public function firstOrNew()
    {
        return InvoiceSetting::firstOrNew(['id' => 1]);
    }

    /**
     * Update settings
     */
    public function updateSettings(array $data)
    {
        $data['org_id'] = auth()->user()->org_id ?? null;
        if (isset($data['store_id'])) {
            $data['branch_id'] = \App\Models\StoreApp\Store::findOrFail($data['store_id'])->branch_id;
        }

        $setting = $this->firstOrNew();
        $setting->fill($data);
        $setting->save();

        // Clear cache so changes take effect immediately
        \Modules\Invoices\App\Helpers\InvoiceHelper::clearCache();

        return $setting;
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
