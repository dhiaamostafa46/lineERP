<?php

namespace App\Repositories;

use App\Models\AccuSoft\TaxAccount;
use App\Repositories\BaseRepository;

class TaxAccountRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'rate',
        'status'
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return TaxAccount::class;
    }

    public function statuses()
    {
        return TaxAccount::statuses();
    }

    public function TaxAccounts()
    {
        return TaxAccount::active()->get()->pluck('name', 'id')->toArray();
    }

    public function getHeaders(): array
    {
        return [
            __('models/tax_accounts.fields.id'),
            __('models/tax_accounts.fields.name'),
            __('models/tax_accounts.fields.rate'),
            __('models/tax_accounts.fields.status'),
            __('models/tax_accounts.fields.created_at')
        ];
    }

    public function dataExcel(): array
    {
        return TaxAccount::with('translations')
            ->get()
            ->map(function ($taxAccount) {
                return [
                    'id' => $taxAccount->id,
                    'name' => $taxAccount->name,
                    'rate' => $taxAccount->rate,
                    'status' => $taxAccount->status_text,
                    'created_at' => $taxAccount->created_at->format('Y-m-d'),
                ];
            })
            ->toArray();
    }

    public function name()
    {
        return __('models/tax_accounts.singular');
    }
}
