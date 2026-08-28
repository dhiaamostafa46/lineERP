<?php

namespace Modules\HR\App\Livewire\Payrolls\Logs;

use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Activitylog\Models\Activity;
use Carbon\Carbon;

class Index extends Component
{
    use WithPagination;

    public function mount() {}

    public function render()
    {
        $now = Carbon::now();
        $month = $now->month;
        
        $data['logs'] = Activity::whereIn('subject_type', [
            'Modules\HR\App\Models\HrPayroll',
            'Modules\HR\App\Models\HrPayrollEmployee',
            'Modules\HR\App\Models\HrPayrollTransaction'
        ])->whereMonth('created_at',">=",$month)->latest()->paginate(10);
         //dd($data['logs']);
        return view('hr::livewire.payrolls.logs.index', $data);
    }
}
