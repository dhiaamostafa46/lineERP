<?php

namespace Modules\Invoices\App\Repositories;

use App\Repositories\BaseRepository;
use Modules\Invoices\App\Models\ZatcaSetting;

class ZatcaSettingRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'vat_number',
        'organization_name',
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return ZatcaSetting::class;
    }

    public function name(): string
    {
        return __('invoices::models/invoices_setting.sections.taxes_and_zakat');
    }

    public function getHeaders(): array
    {
        return [
            __('invoices::models/invoices_setting.fields.id'),
            __('invoices::models/invoices_setting.fields.zatca_vat_number'),
            __('invoices::models/invoices_setting.fields.zatca_organization_name'),
            __('invoices::models/invoices_setting.fields.zatca_environment'),
            __('invoices::models/invoices_setting.fields.zatca_status'),
        ];
    }

    public function dataExcel(): array
    {
        return ZatcaSetting::get()
            ->map(function ($setting) {
                return [
                    'id' => $setting->id,
                    'zatca_vat_number' => $setting->vat_number,
                    'zatca_organization_name' => $setting->organization_name,
                    'zatca_environment' => $setting->environment,
                    'zatca_status' => $setting->status,
                ];
            })
            ->toArray();
    }

    /**
     * Get the ZATCA setting record for the given branch_id (or global if null).
     */
    public function firstOrNew($branch_id = null): ZatcaSetting
    {
        if (empty($branch_id)) {
            $branch_id = \App\Models\Branch::first()->id ?? 1;
        }
        return ZatcaSetting::firstOrNew(['branch_id' => $branch_id]);
    }

    /**
     * Save / update the global ZATCA setting record.
     * Strips the zatca_ prefix from keys automatically.
     *
     * @param  array  $data  Validated request data (keys may have zatca_ prefix)
     * @param  int|null  $branch_id
     */
    public function updateSettings(array $data, $branch_id = null): ZatcaSetting
    {
        if (empty($branch_id)) {
            $branch_id = \App\Models\Branch::first()->id ?? 1;
        }

        // Strip zatca_ prefix if present
        $clean = [];
        foreach ($data as $key => $value) {
            $clean_key = str_starts_with($key, 'zatca_') ? substr($key, 6) : $key;

            // Explicit mapping to database column name if the input name is shorter
            if ($clean_key === 'organization_unit') {
                $clean_key = 'organization_unit_name';
            }

            $clean[$clean_key] = $value;
        }

        // Set the branch_id explicitly from the parameter
        $clean['branch_id'] = $branch_id;
        $clean['establishment_id'] = $clean['establishment_id'] ?? 1;

        // Set default UUID if not exists
        if (empty($clean['uuid'])) {
            $clean['uuid'] = \Illuminate\Support\Str::uuid()->toString();
        }

        $clean['org_id'] = auth()->user()->org_id ?? null;
        if (isset($clean['store_id'])) {
            $clean['branch_id'] = \App\Models\StoreApp\Store::findOrFail($clean['store_id'])->branch_id;
        }

        return ZatcaSetting::updateOrCreate(['branch_id' => $branch_id], $clean);
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
