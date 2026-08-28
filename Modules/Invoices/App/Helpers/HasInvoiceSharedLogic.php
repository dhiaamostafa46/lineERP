<?php

namespace Modules\Invoices\App\Helpers;

use App\Models\AccuSoft\CostCenters;
use App\Models\AccuSoft\TaxAccount;
use App\Models\AccuSoft\TreeAccounts;
use App\Models\Branch;
use App\Models\StoreApp\Store;
use Modules\Invoices\App\Helpers\InvoiceHelper;
use App\Models\invApp\InvSupplier;
use App\Models\invApp\InvCustomer;
use App\Models\invApp\SalesInvoice;

trait HasInvoiceSharedLogic
{
    /**
     * Get common data for invoice forms.
     * Standardizes the "response" of data needed for creation/editing.
     */
    public function getFormData(): array
    {
        return [
            'stores'       => $this->stores(),
            // 'suppliers'    => $this->suppliers(),
            // 'customers'    => $this->customers(),
            'taxes'        => $this->taxes(),
            'taxes_data'   => $this->taxesData(),
            // 'taxAccounts'  => $this->taxAccounts(),
            'payments'     => $this->payments(),
            'cost_centers' => $this->costCenters(),
            'branches'     => $this->branches(),
             'statuses'    =>$this->StatusesInv(),
        ];
    }

    public function stores(): array
    {
        return Store::ActiveOnly()->get()->pluck('name', 'id')->toArray();
    }

    // public function suppliers(): array
    // {
    //     return InvSupplier::ActiveOnly()->get()->pluck('name', 'id')->toArray();
    // }

    // public function customers(): array
    // {
    //     return InvCustomer::ActiveOnly()->get()->pluck('name', 'id')->toArray();
    // }

    public function taxes(): array
    {
        return TaxAccount::active()->get()->pluck('name', 'id')->toArray();
    }

    public function taxesData(): array
    {
        return TaxAccount::active()->get()->mapWithKeys(function ($tax) {
            return [
                $tax->id => [
                    'name' => $tax->name,
                    'rate' => $tax->rate,
                ]
            ];
        })->toArray();
    }

    public function taxAccounts()
    {
        return TaxAccount::active()->get();
    }

    public function branches(): array
    {
        return Branch::ActiveOnly()->get()->pluck('name', 'id')->toArray();
    }

     public function  StatusesInv()
    {
        return SalesInvoice::statusesinv();
    }

    public function costCenters(): array
    {
        return CostCenters::Active()->get()->pluck('name', 'id')->toArray();
    }

    public function payments(): array
    {
        return TreeAccounts::whereIn('account_type', [
            TreeAccounts::ACCOUNT_TYPE_BANK,
            TreeAccounts::ACCOUNT_TYPE_TREASURY
        ])
            ->where('is_leaf', true)
            ->get()
            ->mapWithKeys(function ($account) {
                return [
                    $account->id => [
                        'id' => $account->id,
                        'name' => $account->name,
                        'payment_method' => $account->payment_method ?? 10,
                    ]
                ];
            })
            ->toArray();
    }

    /**
     * Helper to generate standardized invoice numbers.
     */
    protected function generateNumber(string $type = 'purchase', bool $isDraft = false): string
    {
        if ($isDraft) {
            // Simple temporary draft number using timestamp or random string
            return 'DRAFT-' . now()->format('ymd') . '-' . strtoupper(\Illuminate\Support\Str::random(4));
        }

        $settings = InvoiceHelper::getSettings();
        $prefixKey = "{$type}_prefix";
        $nextNumKey = "{$type}_next_number";

        $prefix = $settings->$prefixKey ?? strtoupper(substr($type, 0, 3));
        $nextNum = $settings->$nextNumKey ?? 1;

        $modelClass = $this->model();

        do {
            $number = $prefix . '-' . str_pad($nextNum, 6, '0', STR_PAD_LEFT);
            $exists = $modelClass::where('invoice_number', $number)->exists();
            if ($exists) {
                $nextNum++;
            }
        } while ($exists);

        if ($settings->id) {
            $settings->update([$nextNumKey => $nextNum + 1]);
        }

        return $number;
    }
}

