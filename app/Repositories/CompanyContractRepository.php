<?php

namespace App\Repositories;

use App\Models\CompanyContract;

class CompanyContractRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'company_id',
        'status',
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return CompanyContract::class;
    }

    public function statuses(): array
    {
        return CompanyContract::statuses();
    }

    public function companyPricingTypes(): array
    {
        return CompanyContract::companyPricingTypes();
    }

    public function driverPaymentTypes(): array
    {
        return CompanyContract::driverPaymentTypes();
    }

    public function settlementCycles(): array
    {
        return CompanyContract::settlementCycles();
    }
}
