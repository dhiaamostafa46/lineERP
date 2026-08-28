<?php

namespace Modules\HR\App\Livewire\Statistics\General;

use Carbon\Carbon;
use Livewire\Component;
use Modules\HR\App\Models\HrAdvance;
use Modules\HR\App\Models\HrDepartment;
use Modules\HR\App\Models\HrEmployee;
use Modules\HR\App\Models\HrHoliday;
use Modules\HR\App\Models\HrJustification;
use Modules\HR\App\Models\HrPenalty;
use Modules\HR\App\Models\HrReward;

class EmployeesCounters extends Component
{
    public $counts;
    public $month;
    public $title;

    public function mount()
    {
        $this->month = Carbon::now()->format('Y-m');
        $this->getCounts();
    }

    public function render()
    {
        return view('hr::livewire.statistics.general.employees-counters');
    }

    public function getCounts()
    {
        $this->title = Carbon::parse($this->month)->format('M, Y');
        //comment by saeed
        //$employees = HrEmployee::toBase()->select('id', 'created_at', 'deleted_at')->where('created_at', 'LIKE', $this->month . '%')->get();
       // $employees = HrEmployee::toBase()->select('id', 'created_at', 'deleted_at')->get();
       // dd($employees);
        $justification = HrJustification::toBase()->select('id', 'created_at', 'deleted_at')->where('status' ,HrJustification::STATUS_PENDING)->where('created_at', 'LIKE', $this->month . '%')->get();
        $advances = HrAdvance::toBase()->select('id', 'created_at', 'deleted_at')->where('status' ,HrAdvance::STATUS_PENDING)->where('created_at', 'LIKE', $this->month . '%')->get();
        $holidays = HrHoliday::toBase()->select('id', 'created_at', 'deleted_at')->where('status' ,HrHoliday::STATUS_PENDING)->where('created_at', 'LIKE', $this->month . '%')->get();

        $this->counts = (object)[

            'justification'     => $justification->count(),
            'advances'      => $advances->count(),
            'holidays'      => $holidays->count(),
        ];
    }

    public function updated($propertyName)
    {
        if ($propertyName == 'month') {
            $this->getCounts();
        }
    }
}
