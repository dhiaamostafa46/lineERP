<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Vehicles\Driver;
use App\Models\Vehicles\DriverCompanyReference;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DriverCompanyReferenceTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_one_active_reference_per_driver(): void
    {
        [$driver, $company] = $this->createDriverAndCompany();

        $first = DriverCompanyReference::create([
            'driver_id' => $driver->id,
            'company_id' => $company->id,
            'ref_no' => 'REF-001',
            'status' => DriverCompanyReference::STATUS_ACTIVE,
        ]);

        $second = DriverCompanyReference::create([
            'driver_id' => $driver->id,
            'company_id' => $company->id,
            'ref_no' => 'REF-002',
            'status' => DriverCompanyReference::STATUS_ACTIVE,
        ]);

        $first->refresh();
        $second->refresh();

        $this->assertSame(DriverCompanyReference::STATUS_SUSPENDED, $first->status);
        $this->assertNotNull($first->ended_at);
        $this->assertSame(DriverCompanyReference::STATUS_ACTIVE, $second->status);
        $this->assertSame(1, DriverCompanyReference::query()->activeOnly()->where('driver_id', $driver->id)->count());
    }

    public function test_ref_no_is_unique_per_company(): void
    {
        [$driver, $company] = $this->createDriverAndCompany();

        DriverCompanyReference::create([
            'driver_id' => $driver->id,
            'company_id' => $company->id,
            'ref_no' => 'REF-SHARED',
            'status' => DriverCompanyReference::STATUS_COMPLETED,
        ]);

        $this->expectException(\Illuminate\Database\QueryException::class);

        DriverCompanyReference::create([
            'driver_id' => $driver->id,
            'company_id' => $company->id,
            'ref_no' => 'REF-SHARED',
            'status' => DriverCompanyReference::STATUS_ACTIVE,
        ]);
    }

    /**
     * @return array{0: Driver, 1: Company}
     */
    private function createDriverAndCompany(): array
    {
        $driver = Driver::create([
            'iqama' => '1234567890',
            'name' => 'Test Driver',
            'status' => Driver::STATUS_ACTIVE,
        ]);

        $company = Company::create([
            'code' => 'TST-'.uniqid(),
            'status' => Company::STATUS_ACTIVE,
        ]);
        $company->translateOrNew('en')->name = 'Test Company';
        $company->save();

        return [$driver, $company];
    }
}
