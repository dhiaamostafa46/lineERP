<?php

namespace Modules\HR\App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;

use Modules\HR\App\Models\HrHolidayType;

class MyRequestsCreate extends Component
{
    use WithPagination;

    public $request_type;
    public $holiday_types;
    public $employee_id;
    public $tab;

    public function mount()
    {
        $this->tab = auth()->user()->employee->tab ?? 'main';
        $this->employee_id = auth()->user()->employee->hrEmployee->id ?? 0;


        if ($this->tab == 'vacations') {
            $this->request_type = 'holidays';
            $this->holiday_types = HrHolidayType::get()->pluck('name', 'id');
        } elseif ($this->tab == 'advances') {
            $this->request_type = 'advances';
        } else {
               $this->holiday_types = HrHolidayType::get()->pluck('name', 'id');
            $this->request_type = 'holidays'; // Default to holidays
        }
    }


    public function render()
    {
        return view('hr::livewire.my_requests_create');
    }
}
