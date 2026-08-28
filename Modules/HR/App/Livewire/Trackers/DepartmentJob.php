<?php

namespace Modules\HR\App\Livewire\Trackers;

use Livewire\Component;
use Modules\HR\App\Models\HrDepartment;

class DepartmentJob extends Component
{
    public $department_id;
    public $departments;
    public $department_jobs = [];
    public $tracked_jobs = [];

    function mount()
    {
        $this->getJobs();
        $this->departments = HrDepartment::get()->pluck('name', 'id');
    }

    public function render()
    {
        return view('hr::livewire.trackers.department-job');
    }

    public function updated($property_name, $property_value)
    {
        if ($property_name == 'department_id') {
            $this->department_id = $property_value;
            $this->department_jobs = [];
            $department = HrDepartment::find($this->department_id);
            if ($department) {
                $this->department_jobs = $department->jobs->pluck('name', 'id');
            }
        }
    }

    public function getJobs()
    {
        $department = HrDepartment::find($this->department_id);
        if ($department) {
            $this->department_jobs = $department->jobs->pluck('name', 'id');
        }
    }
}
