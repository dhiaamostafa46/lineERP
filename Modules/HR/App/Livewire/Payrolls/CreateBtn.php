<?php

namespace Modules\HR\App\Livewire\Payrolls;

use Carbon\Carbon;
use Livewire\Component;
use Modules\HR\App\Models\HrSetting;
use Modules\HR\App\Jobs\PreparingPayrollJob;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class CreateBtn extends Component
{
    use LivewireAlert;

    public $payroll_date;
    public $setting;
    public $delivery_at;
    public $preparing_at;
    public $openModal;
    public $status;
    public $current_month;

    public function mount()
    {
        $this->openModal = false;
        $this->status = 'open';
    }

    public function getData()
    {
        //payroll_date
        $this->getSetting();
        $setting = $this->setting;
        $next_month = Carbon::now()->addMonth(1)->format('Y-m');
       $this->current_month = Carbon::now()->format('Y-m');

       //$this->current_month = Carbon::now()->addMonth(1)->format('Y-m');
        //$this->payroll_date = Carbon::parse($setting->next_payroll_date)->format('Y-m') ?? $this->current_month;
        $this->payroll_date = Carbon::parse($setting->next_payroll_date)->format('Y-m') ?? $this->current_month;

        $this->delivery_at = $next_month . '-' . $setting->delivery_payroll_at;
        $this->preparing_at = Carbon::now()->format('Y-m-d');
        if ($setting->preparing_payroll) {
            $this->status = $setting->payroll_status;
            if ($this->status == HrSetting::PAYROLL_STATUS_IN_PROGRESS && $setting->payroll_status) {
                $this->status = HrSetting::PAYROLL_STATUS_READY;
            }

        }
    }

    public function render()
    {
        $this->getData();
        return view('hr::livewire.payrolls.create-btn');
    }

    public function toggleOpenModal()
    {
        $this->openModal = !$this->openModal;
    }

      public function getSetting()
    {
        // Attempt to retrieve the first settings record
        $this->setting = HrSetting::first();

        // If no record exists, insert default values and fetch it again
        if (!$this->setting) {
            // Insert default settings into the database
            $this->setting = HrSetting::create([
                'delivery_payroll_at'  => '5',
                'preparing_payroll_at' => '25',
                'min_salary'           => 3000.00,
                'max_off_days'         => 14,
                'currency'             => 'SAR',
                'next_payroll_date'    => Carbon::now()->format('Y-m-d'), // example value if needed
                'payroll_status'       => HrSetting::PAYROLL_STATUS_READY // example constant if applicable
            ]);
        }
    }

    public function createPayroll()
    {
        if ($this->setting->preparing_payroll) {
            $this->alert('warning', 'Payroll is preparing');
            return;
        }
        $next_payroll_date = Carbon::parse($this->payroll_date)->addMonth(1)->format('Y-m') . '-' . $this->setting->preparing_payroll_at;
        // run job PreparingPayrollJob
        PreparingPayrollJob::dispatch($this->payroll_date, auth()->user(), $this->delivery_at);


        
        $this->setting->update([
            'next_payroll_date' => $next_payroll_date,
            'preparing_payroll' => true,
            'payroll_status' => HrSetting::PAYROLL_STATUS_IN_PROGRESS,
        ]);
        $this->openModal = false;
        $this->status = HrSetting::PAYROLL_STATUS_IN_PROGRESS;
        $this->getData();
        $this->alert('success', __('hr::models/hr_payrolls.fields.start_payroll'));

        $this->render();
    }

    public function refreshPage()
    {
        $this->setting->update([
            'payroll_status' => HrSetting::PAYROLL_STATUS_CLOSED,
        ]);
        $this->redirect(route('hr.payrolls.index'));
    }
}
