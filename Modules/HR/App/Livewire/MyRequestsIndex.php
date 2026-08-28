<?php

namespace Modules\HR\App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Modules\HR\App\Models\HrAdvance;
use Modules\HR\App\Models\HrHoliday;

class MyRequestsIndex extends Component
{
    use WithPagination;

    public $request_type;
    public function mount()
    {
        $this->request_type = 'holidays';
    }

    public function setRequestType($type)
    {
        $this->request_type = $type;
    }

    public function render()
    {
       
        switch ($this->request_type) 
        {
            case 'holidays':
                $data['holidays'] = HrHoliday::where('employee_id', auth()->user()->employee->id)->paginate(10);
                break;
            case 'advances':
                $data['advances'] = HrAdvance::where('employee_id', auth()->user()->employee->id)->paginate(10);
        }

        return view('hr::livewire.my_requests_index', $data);
    }
}
