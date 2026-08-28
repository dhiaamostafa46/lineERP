<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Employee;
use App\Models\Organization;
use App\Models\StoreApp\Store;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\HR\App\Models\HrAllowance;
use Modules\HR\App\Models\HrContractType;
use Modules\HR\App\Models\HrDeduct;
use Modules\HR\App\Models\HrDepartment;
use Modules\HR\App\Models\HrDocumentType;
use Modules\HR\App\Models\HrHolidayType;
use Modules\HR\App\Models\HrJob;
use Modules\HR\App\Models\HrPlace;
use Modules\HR\App\Models\HrReportType;
use Modules\HR\App\Models\HrShiftType;

class TestDataSeeder extends Seeder
{
    public $employee_data = [];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        Organization::create([
            'ar' => ['name' => 'المنشاة'],
            'en' => ['name' => 'Organization'],
            'status' => 1,
        ]);

        Branch::create([
            'ar' => ['name' => 'الرئيسي'],
            'en' => ['name' => 'main'],
            'status' => 1,
            'org_id' => 1,
        ]);

        //  Store::create([
        //     'ar'               => ['name' => 'الرئيسي'],
        //     'en'               => ['name' => 'main'],
        //     'status'           =>1 ,
        //     'org_id'           =>1 ,
        //     'branch_id'           =>1 ,
        // ]);

        HrJob::create([
            'en' => ['name' => 'CEO'],
            'ar' => ['name' => 'المدير العام'],
            'status' => HrJob::STATUS_ACTIVE,
            'license_required' => HrJob::LICENSE_REQUIRED_NO,
        ]);
        HrJob::create([
            'en' => ['name' => 'Department Manager'],
            'ar' => ['name' => 'مدير قسم'],
            'status' => HrJob::STATUS_ACTIVE,
            'license_required' => HrJob::LICENSE_REQUIRED_NO,
        ]);
        HrJob::create([
            'en' => ['name' => 'Account Manager'],
            'ar' => ['name' => 'مدير الحسابات'],
            'status' => HrJob::STATUS_ACTIVE,
            'license_required' => HrJob::LICENSE_REQUIRED_YES,
        ]);
        HrJob::create([
            'en' => ['name' => 'accountant'],
            'ar' => ['name' => 'محاسب'],
            'status' => HrJob::STATUS_ACTIVE,
            'license_required' => HrJob::LICENSE_REQUIRED_YES,
        ]);

        HrDepartment::create([
            'en' => ['name' => 'General Administration'],
            'ar' => ['name' => 'الادارة العامة'],
            'type' => HrDepartment::TYPE_DEPARTMENT,
            'status' => HrDepartment::STATUS_ACTIVE,
            'code' => 'accounts',
        ]);
        HrDepartment::create([
            'en' => ['name' => 'The Accounts'],
            'ar' => ['name' => 'الحسابات'],
            'type' => HrDepartment::TYPE_DEPARTMENT,
            'status' => HrDepartment::STATUS_ACTIVE,
            'code' => 'accounts',
        ]);
        HrDepartment::create([
            'en' => ['name' => 'HR'],
            'ar' => ['name' => 'الموارد البشرية'],
            'type' => HrDepartment::TYPE_DEPARTMENT,
            'status' => HrDepartment::STATUS_ACTIVE,
            'code' => 'accounts',
        ]);
        $shift_type = HrShiftType::create([
            'en' => ['name' => 'Full Time Morning'],
            'ar' => ['name' => 'الدوام الكامل الصباح'],
            'status' => HrShiftType::STATUS_ACTIVE,
            'type' => HrShiftType::TYPE_STATIC,
            'work_hours' => 8,
        ]);

        $shift_type->shifts()->create([
            'from' => '08:00:00',
            'to' => '12:00:00',
        ]);
        $shift_type->shifts()->create([
            'from' => '4:00:00',
            'to' => '8:00:00',
        ]);
        $shift_type = HrShiftType::create([
            'en' => ['name' => 'Full Time Evening'],
            'ar' => ['name' => 'الدوام الكامل مساء'],
            'status' => HrShiftType::STATUS_ACTIVE,
            'type' => HrShiftType::TYPE_STATIC,
            'work_hours' => 8,
        ]);

        $shift_type->shifts()->create([
            'from' => '08:00:00',
            'to' => '4:00:00',
        ]);

        HrDocumentType::create([
            'en' => ['name' => 'Passport'],
            'ar' => ['name' => 'صورة الباسبور'],
            'status' => HrDocumentType::STATUS_ACTIVE,
        ]);
        HrDocumentType::create([
            'en' => ['name' => 'ID Card'],
            'ar' => ['name' => 'بطاقة الرقم القومي'],
            'status' => HrDocumentType::STATUS_ACTIVE,
        ]);

        HrContractType::create([
            'en' => ['name' => 'Full Time'],
            'ar' => ['name' => 'عقد دوام كامل'],
            'status' => HrContractType::STATUS_ACTIVE,
        ]);

        HrContractType::create([
            'en' => ['name' => 'Part Time'],
            'ar' => ['name' => 'عقد دوام جزئي'],
            'status' => HrContractType::STATUS_ACTIVE,
        ]);

        // HrHolidayType::create([
        //     'en'       => ['name' => 'Annual'],
        //     'ar'       => ['name' => 'سنوي'],
        //     'off_days' => 1,
        //     'type' =>  HrHolidayType::TYPE_WITH_DEDUCTION,
        //     'status'   => HrHolidayType::STATUS_ACTIVE,
        // ]);

        // HrHolidayType::create([
        //     'en'       => ['name' => 'Sick'],
        //     'ar'       => ['name' => 'مرضى'],
        //     'off_days' => 3,
        //     'type' =>  HrHolidayType::TYPE_WITHOUT_DEDUCTION,
        //     'status'   => HrHolidayType::STATUS_ACTIVE,
        // ]);

        // HrHolidayType::create([
        //     'en'       => ['name' => 'Personal'],
        //     'ar'       => ['name' => 'شخصي'],
        //     'off_days' => 1,
        //     'type' =>  HrHolidayType::TYPE_WITH_DEDUCTION,
        //     'status'   => HrHolidayType::STATUS_ACTIVE,
        // ]);

        // HrHolidayType::create([
        //     'en'       => ['name' => 'Family'],
        //     'ar'       => ['name' => 'عائلي'],
        //     'off_days' => 1,
        //      'type' =>  HrHolidayType::TYPE_WITH_DEDUCTION,
        //     'status'   => HrHolidayType::STATUS_ACTIVE,
        // ]);

        // HrHolidayType::create([
        //     'en'       => ['name' => 'Other'],
        //     'ar'       => ['name' => 'اخرى'],
        //     'off_days' => 1,
        //      'type' =>  HrHolidayType::TYPE_WITH_DEDUCTION,
        //     'status'   => HrHolidayType::STATUS_ACTIVE,
        // ]);

        HrAllowance::create([
            'en' => ['name' => 'Allowance'],
            'ar' => ['name' => 'العلاوات'],
            'status' => HrAllowance::STATUS_ACTIVE,
        ]);

        HrDeduct::create([
            'en' => ['name' => 'Deduct'],
            'ar' => ['name' => 'الخصم'],
            'status' => HrDeduct::STATUS_ACTIVE,
        ]);

        HrReportType::create([
            'en' => ['name' => 'HR Report', 'description' => 'Employees & Salary'],
            'ar' => ['name' => 'تقرير المواد البشرية', 'description' => 'تقرير الموظفين والراتب'],
        ]);

        $workDays = [
            HrPlace::DAY_SUNDAY,
            HrPlace::DAY_MONDAY,
            HrPlace::DAY_TUESDAY,
            HrPlace::DAY_WEDNESDAY,
            HrPlace::DAY_THURSDAY,
        ];

        HrPlace::create([
            'name' => 'مقر الإدارة العامة - الرياض',
            'lat' => (string) fake()->randomFloat(6, 24.55, 24.85),
            'lon' => (string) fake()->randomFloat(6, 46.55, 46.95),
            'address' => 'الرياض، المملكة العربية السعودية',
            'status' => HrPlace::STATUS_ACTIVE,
            'distance' => 200,
            'flage' => 3,
            'department_id' => ["1"],
            'day' => $workDays,
        ]);

        HrPlace::create([
            'name' => 'مقر الحسابات - الرياض',
            'lat' => (string) fake()->randomFloat(6, 24.55, 24.85),
            'lon' => (string) fake()->randomFloat(6, 46.55, 46.95),
            'address' => 'الرياض، المملكة العربية السعودية',
            'status' => HrPlace::STATUS_ACTIVE,
            'distance' => 200,
            'flage' => 3,
            'department_id' => ["2"],
            'day' => $workDays,
        ]);

        $now = now();
        // if (!DB::table('employees')->first()) {
        //     DB::table('employees')->insert([
        //         'user_id'            => 1,
        //         'username'           => 'user',
        //         'full_name'          => 'user',
        //         'email'              => 'user@email.com',
        //         'phone'              => '00201156215932',
        //         'dob'                => '1991-07-07',
        //         'address'            => 'Cairo, Egypt',
        //         'national_address'   => 'Cairo, Egypt',
        //         'religion'           => 'مسلم',
        //         'gender'             => 1,
        //         'marital_status'     => 1,
        //         'number_of_children' => 0,
        //         'nationality'        => 'مصري',
        //         'created_at'         => $now,
        //         'updated_at'         => $now,
        //     ]);
        // }
        // if (!DB::table('hr_employees')->first()) {
        //     DB::table('hr_employees')->insert([
        //         'username'           => 'user',
        //         'department_id'      => rand(1, 3),
        //         'job_id'             => rand(1, 4),
        //         'shift_id'           => rand(1, 2),
        //         'max_off_days'       => 14,
        //         'max_advance'        => 1500.00,
        //         'job_level'          => 'Senior',
        //         'specialty'          => 'Accounting',
        //         'start_at'           => now(),
        //         'license_expired_at' => now()->addMonths(6),
        //         'user_id'            => 1,
        //         'employee_id'        => 1,
        //         'created_at'         => $now,
        //         'updated_at'         => $now,
        //     ]);
        //     DB::table('employee_identities')->insert([
        //         'employee_id'          => 1,
        //         'identity_type'        => rand(1, 2),
        //         'identity_no'          => rand(1111111111, 9999999999),
        //         'insurance_no'         => rand(1111111111, 9999999999),
        //         'identity_expired_at'  => now()->addMonths(rand(1, 6)),
        //         'insurance_expired_at' => now()->addMonths(rand(1, 6)),
        //         'created_at'           => $now,
        //         'updated_at'           => $now
        //     ]);
        //     $banks = array_keys(config('banks'));
        //     $iban  = $banks[rand(0, 10)];
        //     DB::table('employee_banks')->insert([
        //         'employee_id' => 1,
        //         'iban'        => 'SA' . $iban . rand(1111111111111111, 9999999999999999),
        //         'bank_name'   => config('banks')[$iban],
        //         'created_at'  => $now,
        //         'updated_at'  => $now
        //     ]);
        // }
        // if (!DB::table('hr_salaries')->first()) {
        //     DB::table('hr_salaries')->insert([
        //         'employee_id' => 1,
        //         'basic'       => 3000,
        //         'created_at'  => $now,
        //         'updated_at'  => $now
        //     ]);
        // }

        // $banks = array_keys(config('banks'));

        // Employee::factory(1)->create()->each(function ($employee) use ($banks, $now) {
        //     $this->employee_data['users'][] = [
        //         'name'     => $employee->username,
        //         'email'    => $employee->email,
        //         'phone'    => $employee->phone,
        //         'password' => bcrypt('password'),
        //     ];
        //     $this->employee_data['hr_employees'][] = [
        //         'employee_id'        => $employee->id,
        //         'username'           => $employee->username,
        //         'department_id'      => rand(1, 3),
        //         'job_id'             => rand(1, 4),
        //         'shift_id'           => rand(1, 2),
        //         'max_off_days'       => 14,
        //         'max_advance'        => 1500.00,
        //         'job_level'          => 'Senior',
        //         'specialty'          => 'Accounting',
        //         'start_at'           => $now,
        //         'license_expired_at' => Carbon::now()->addMonths(6),
        //         'user_id'            => $employee->id,
        //         'created_at'         => $now,
        //         'updated_at'         => $now
        //     ];

        //     $this->employee_data['hr_salaries'][] = [
        //         'employee_id' => $employee->id,
        //         'basic'       => rand(1000, 5000),
        //         'created_at'  => $now,
        //         'updated_at'  => $now
        //     ];
        //     $this->employee_data['identities'][] = [
        //         'employee_id'          => $employee->id,
        //         'identity_type'        => rand(1, 2),
        //         'identity_no'          => rand(1111111111, 9999999999),
        //         'insurance_no'         => rand(1111111111, 9999999999),
        //         'identity_expired_at'  => Carbon::now()->addMonths(rand(1, 6)),
        //         'insurance_expired_at' => Carbon::now()->addMonths(rand(1, 6)),
        //         'created_at'           => $now,
        //         'updated_at'           => $now
        //     ];
        //     $iban                           = $banks[rand(0, 10)];
        //     $this->employee_data['banks'][] = [
        //         'employee_id' => $employee->id,
        //         'iban'        => 'SA' . $iban . rand(1111111111111111, 9999999999999999),
        //         'bank_name'   => config('banks')[$iban],
        //         'created_at'  => $now,
        //         'updated_at'  => $now
        //     ];

        //     // $user = User::create([
        //     //     'name' => $employee->username,
        //     //     'email' => $employee->email,
        //     //     'password'  => 'password',
        //     // ]);
        //     // $hr_employee = HrEmployee::create([
        //     //     'employee_id'        => $employee->id,
        //     //     'username'           => $employee->username,
        //     //     'department_id'      => rand(1, 3),
        //     //     'job_id'             => rand(1, 4),
        //     //     'shift_id'           => rand(1, 2),
        //     //     'max_off_days'       => 14,
        //     //     'max_advance'        => 1500.00,
        //     //     'job_level'          => 'Senior',
        //     //     'specialty'          => 'Accounting',
        //     //     'start_at'           => now(),
        //     //     'license_expired_at' => now()->addMonths(6),
        //     //     'user_id'            => $user->id,
        //     // ]);
        //     // $hr_employee->salary()->create([
        //     //     'basic' => rand(1000, 5000),
        //     // ]);

        //     // $employee->identity()->create([
        //     //     'identity_type' => rand(1, 2),
        //     //     'identity_no'   => rand(1111111111, 9999999999),
        //     //     'insurance_no'  => rand(1111111111, 9999999999),
        //     //     'identity_expired_at' => now()->addMonths(rand(1, 6)),
        //     //     'insurance_expired_at' => now()->addMonths(rand(1, 6)),
        //     // ]);
        //     // $iban = $banks[rand(0, 10)];
        //     // $employee->bank()->create([
        //     //     'iban' => 'SA' . $iban . rand(1111111111111111, 9999999999999999),
        //     //     'bank_name' => config('banks')[$iban],
        //     // ]);
        // });
        // DB::table('users')->insert($this->employee_data['users']);
        // DB::table('hr_employees')->insert($this->employee_data['hr_employees']);
        // DB::table('hr_salaries')->insert($this->employee_data['hr_salaries']);
        // DB::table('employee_identities')->insert($this->employee_data['identities']);
        // DB::table('employee_banks')->insert($this->employee_data['banks']);
    }
}
