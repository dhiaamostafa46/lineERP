<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\HR\App\Models\HrSalary;
use Modules\HR\App\Models\HrPayroll;
use Modules\HR\App\Models\HrPayrollEmployee;
use Modules\HR\App\Models\HrSetting;
use Modules\HR\App\Models\HrPayrollTransaction;

class updatePayrollEmployees extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-payroll-employees';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Updating Payroll Employees...');
        $setting = HrSetting::first();


        if ($setting->payroll_id) {
            $this->info('Updating Payroll...' );
            $payroll = HrPayroll::find($setting->payroll_id);

            $employees = HrPayrollEmployee::where('payroll_id', $payroll->id)->get();
            // dd($employees);

            $sum_sal = 0;
            foreach ($employees as $employee) {


                // dd($employee);
                $total_allowances = HrPayrollTransaction::where([

                    'type' => HrPayrollTransaction::TYPE_ALLOWANCE,
                    'payroll_employee_id' => $employee->id
                ])->get()->sum('amount');

                $total_deducts = HrPayrollTransaction::where([

                    'type' => HrPayrollTransaction::TYPE_DEDUCT,
                    'payroll_employee_id' => $employee->id
                ])->get()->sum('amount');


                $total_penalties = HrPayrollTransaction::where([

                    'type' => HrPayrollTransaction::TYPE_PENALTY,
                    'payroll_employee_id' => $employee->id
                ])->get()->sum('amount');


                $total_advances = HrPayrollTransaction::where([

                    'type' => HrPayrollTransaction::TYPE_ADVANCE,
                    'payroll_employee_id' => $employee->id
                ])->get()->sum('amount');


                $total_rewards = HrPayrollTransaction::where([
                  
                    'type' => HrPayrollTransaction::TYPE_REWARD,
                    'payroll_employee_id' => $employee->id
                ])->get()->sum('amount');


                $basic_wage = HrSalary::where('employee_id', $employee->employee_id)->first()->basic;
                // if ($employee->employee_id == 346) {
                //     dd($basic_wage, $total_allowances, $total_deducts, $total_penalties, $total_advances, $total_rewards);
                // }
                $net_wage=($basic_wage + $total_allowances + $total_rewards) - ($total_deducts + $total_penalties + $total_advances);
                $employee->update([
                    'basic_wage' => $basic_wage,
                    'total_allowances' => $total_allowances,
                    'total_deducts' => $total_deducts,
                    'total_penalties' => $total_penalties,
                    'total_advances' => $total_advances,
                    'total_rewards' => $total_rewards,
                    'net_wage' => $net_wage,
                ]);
                $sum_sal+=$net_wage;
            }


            //$payroll->update(['total' => $payroll->payroll_employees()->get()->sum('net_wage')]);Comment by saeed
            $payroll->update(['total' => $sum_sal]);
            $this->info('Payroll Updated');
           // __('hr::models/hr_payrolls.fields.payroll_updated'))

           //added by saeed
           $setting->payroll_updated=0;
           $setting->save();
        }
        $this->info('The command was successful...');
    }
}
