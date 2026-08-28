<?php

namespace Modules\HR\App\Jobs;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Modules\HR\App\Models\HrSetting;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\HR\App\Repositories\HrPayrollRepository;
use Modules\HR\App\Repositories\HrSettingRepository;
use Modules\HR\App\Repositories\HrPayrollEmployeeRepository;
use Modules\HR\App\Repositories\HrPayrollTransactionRepository;

class PreparingPayrollJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $payroll_date;
    public $user;
    public $delivery_at;
    /**
     * Create a new job instance.
     */
    public function __construct($payroll_date, $user, $delivery_at)
    {
        $this->payroll_date = $payroll_date;
        $this->user = $user;
        $this->delivery_at = $delivery_at;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // getting

        $setting = HrSetting::first();
        if ($setting->preparing_payroll) {
            return;
        }
        $settingRepo = new HrSettingRepository;
        $payrollRepo = new HrPayrollRepository;
        $employeeRepo = new HrPayrollEmployeeRepository;
        $transactionRepo = new HrPayrollTransactionRepository;
        $payroll_employees = $payrollRepo->payroll_employees();

        //dd( $payroll_employees);
        // preparing
        $input = [];
        $input['delivery_at']  = $this->delivery_at;
        $input['payroll_date'] = $this->payroll_date;
        $input['total']        = $payroll_employees->sum('net_wage');
        $input['preparing_at']  = Carbon::now()->format('Y-m-d');

        // storing
        $payroll = $payrollRepo->create($input, false);

        $employees = $employeeRepo->updateOrCreateMany($payroll_employees, $payroll->id);
        $transactionRepo->syncEmployees($employees, $payroll->id ,  $payroll->payroll_date);
        $payrollRepo->create_approvals($payroll, $setting->approval_payroll ?? []);
        $settingRepo->update(
            [
                'payroll_id'        => $payroll->id,
                'due_payroll_at'    => $payroll->delivery_at,
                'preparing_payroll' => false,
                'payroll_status'    => HrSetting::PAYROLL_STATUS_READY,
            ],
            $setting->id
        );

        activity()
            ->causedBy($this->user)
            ->on($payroll)
            ->withProperties($input)
            ->event(__('hr::models/hr_payrolls.fields.preparing_Payroll'))
            ->log(__('hr::models/hr_payrolls.fields.preparing_Payroll'));
    }
}
