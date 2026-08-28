<?php

namespace Modules\Finance\App\Repositories;

use App\Models\AccuSoft\CostCenters;
use Modules\Finance\App\Models\FncBond;
use App\Models\Branch;

use App\Models\AccuSoft\TreeAccounts;
use App\Repositories\BaseRepository;

class FncBondRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'voucher_number',
        'bond_type',
        'status',
        'date'
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return FncBond::class;
    }

    public function statuses(): array
    {
        return FncBond::statuses();
    }

    public function types(): array
    {
        return FncBond::types();
    }

    public function listItems($id)
    {
        return FncBond::findOrFail($id);
    }

    public function fundAccounts()
    {
        return TreeAccounts::whereIn('account_type', [TreeAccounts::ACCOUNT_TYPE_TREASURY, TreeAccounts::ACCOUNT_TYPE_BANK])->where('is_leaf', true)->active()->get()->pluck('name', 'id');
    }

    public function contactAccounts()
    {
        return TreeAccounts::active()->where('is_leaf', true)->get()->pluck('name', 'id');
    }

    public function costCenters()
    {
        return CostCenters::active()->get()->pluck('name', 'id');
    }

    public function branches()
    {
        return Branch::activeOnly()->get()->pluck('name', 'id');
    }

    public function header()
    {
        return [
            __('finance::models/fnc_bond.fields.voucher_number'),
            __('finance::models/fnc_bond.fields.bond_type'),
            __('finance::models/fnc_bond.fields.date'),
            __('finance::models/fnc_bond.fields.amount'),
            __('finance::models/fnc_bond.fields.status'),
            __('finance::models/fnc_bond.fields.created_at')
        ];
    }

    public function dataExel(): array
    {
        return FncBond::with(['fundAccount', 'contactAccount'])
            ->get()
            ->map(function ($bond) {
                return [
                    'voucher_number' => $bond->voucher_number,
                    'bond_type' => $bond->type_text,
                    'date' => $bond->date ? $bond->date->format('Y-m-d') : '',
                    'amount' => $bond->amount,
                    'status' => $bond->status_text,
                    'created_at' => $bond->created_at->format('Y-m-d'),
                ];
            })
            ->toArray();
    }

    public function getdataorganization()
    {
        return \App\Models\Organization::first();
    }

    public function name()
    {
        return __('finance::models/fnc_bond.plural');
    }
}
