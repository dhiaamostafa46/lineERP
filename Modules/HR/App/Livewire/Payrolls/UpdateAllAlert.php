<?php

namespace Modules\HR\App\Livewire\Payrolls;

use Livewire\Component;
use Illuminate\Support\Facades\Artisan;
use Modules\HR\App\Livewire\Payrolls\Employees\Index;

class UpdateAllAlert extends Component
{

    public function render()
    {
        return view('hr::livewire.payrolls.update-all-alert');
    }

    public function syncPayroll()
    {


        Artisan::call('app:update-payroll-employees');
        $this->dispatch('employees-updated')->to(Index::class);
       // $hr_setting->payroll_updated
    }
}
