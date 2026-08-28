<?php

namespace Modules\HR\database\seeders;

use Illuminate\Database\Seeder;
use Modules\HR\App\Models\HrSetting;

class SettingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        HrSetting::create([
            'delivery_payroll_at'  => '26',
            'preparing_payroll_at' => '5',
            'min_salary'           => 3000.00,
            'max_off_days'         => 14,
            'currency'             => 'SAR',
        ]);
    }
}
