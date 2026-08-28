<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\HR\App\Models\HrEmployee;
use Modules\HR\App\Models\HrSalary;

class EmployeeSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $branchId = 1;
            $orgId = 1;
            $employeeRole = Role::where('name', 'موظف')->first();

            $banks = array_keys(config('banks', []));
            $bankCode = $banks[0] ?? '10';

            $employeesData = [
                [
                    'full_name' => 'أحمد محمد',
                    'username' => 'ahmed.mohamed',
                    'email' => 'ahmed@example.com',
                    'phone' => '0500000001',
                    'gender' => Employee::GENDER_MALE,
                    'marital_status' => Employee::MARITAL_STATUS_MARRIED,
                    'job_number' => 'EMP-001',
                    'basic' => 8000,
                ],
                [
                    'full_name' => 'سارة علي',
                    'username' => 'sara.ali',
                    'email' => 'sara@example.com',
                    'phone' => '0500000002',
                    'gender' => Employee::GENDER_FEMALE,
                    'marital_status' => Employee::MARITAL_STATUS_SINGLE,
                    'job_number' => 'EMP-002',
                    'basic' => 6000,
                ],
            ];

            $directManagerId = null;

            foreach ($employeesData as $index => $data) {
                $user = User::create([
                    'name' => $data['full_name'],
                    'email' => $data['email'],
                    'phone' => $data['phone'],
                    'password' => 'Evix20',
                    'status' => User::STATUS_ACTIVE,
                    'branch_id' => $branchId,
                    'org_id' => $orgId,
                    'job_number' => $data['job_number'],
                ]);

                if ($employeeRole) {
                    $user->assignRole($employeeRole);
                }

                $employee = Employee::create([
                    'user_id' => $user->id,
                    'branch_id' => $branchId,
                    'org_id' => $orgId,
                    'full_name' => $data['full_name'],
                    'username' => $data['username'],
                    'phone' => $data['phone'],
                    'email' => $data['email'],
                    'dob' => '1990-01-15',
                    'address' => 'الرياض',
                    'national_address' => 'الرياض - حي النخيل',
                    'religion' => 'مسلم',
                    'gender' => $data['gender'],
                    'marital_status' => $data['marital_status'],
                    'number_of_children' => 0,
                    'nationality' => 'سعودي',
                ]);

                $hrEmployee = HrEmployee::create([
                    'employee_id' => $employee->id,
                    'user_id' => $user->id,
                    'username' => $data['username'],
                    'department_id' => $index + 1,
                    'job_id' => $index + 1,
                    'shift_id' => $index + 1,
                    'max_off_days' => 21,
                    'max_advance' => 2000,
                    'vacation_balance' => 5,
                    'job_level' => 'Senior',
                    'specialty' => 'General',
                    'start_at' => Carbon::now()->subMonths(6)->toDateString(),
                    'license_expired_at' => Carbon::now()->addYear()->toDateString(),
                    'Direct_manager' => $directManagerId,
                    'job_number' => $data['job_number'],
                    'fingerprint_exempt' => HrEmployee::FINGERPRINT_EXEMPT_FALSE,
                    'attendance_type' => HrEmployee::ATTENDANCE_GEOGRAPHIC,
                ]);

                if ($index === 0) {
                    $directManagerId = $hrEmployee->id;
                }

                $employee->identity()->create([
                    'identity_type' => 1,
                    'identity_no' => '1'.str_pad((string) ($index + 1), 9, '0', STR_PAD_LEFT),
                    'insurance_no' => 'INS-'.($index + 1),
                    'identity_expired_at' => Carbon::now()->addYear()->toDateString(),
                    'insurance_expired_at' => Carbon::now()->addYear()->toDateString(),
                ]);

                $employee->bank()->create([
                    'iban' => 'SA'.$bankCode.str_pad((string) ($index + 1), 18, '0', STR_PAD_LEFT),
                    'bank_name' => config("banks.{$bankCode}", 'Al Rajhi Bank'),
                ]);

                HrSalary::create([
                    'employee_id' => $hrEmployee->id,
                    'basic' => $data['basic'],
                ]);
            }
        });
    }
}
