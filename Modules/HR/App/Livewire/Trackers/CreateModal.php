<?php

namespace Modules\HR\App\Livewire\Trackers;

use App\Models\User;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use Modules\HR\App\Models\HrJob;
use Livewire\WithoutUrlPagination;
use Modules\HR\App\Models\HrTracker;
use Modules\HR\App\Models\HrEmployee;
use Modules\HR\App\Models\HrDepartment;

class CreateModal extends Component
{
    use WithPagination, WithoutUrlPagination;

    public $employees;
    public $department_id;
    public $users;
    public $jobs;
    public $types;
    public $statuses;
    public HrDepartment $department;
    public $name;
    public $type;
    public $status;
    public $tracker_approvals;
    public $openModal;

    public function mount()
    {
        $this->openModal = false;
    }

    public function render()
    {
        $data['trackers'] = HrTracker::where('department_id', $this->department_id)
            ->paginate(10);
        return view('hr::livewire.trackers.create-modal', $data);
    }

    public function rules(): array
    {
        return [
            'department_id' => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'department_id.required' => 'The department field is required.',
        ];
    }

    public function getEmployees()
    {
        $this->employees = HrEmployee::where('department_id', $this->department->id)->get();
    }

    public function getUsers()
    {
        $this->users = User::whereIn('id', $this->employees->pluck('user_id')->toArray())
            ->get()
            ->pluck('name', 'id')
            ->toArray();
    }

    public function getJobs()
    {
        $this->jobs = HrJob::whereIn('id', $this->employees->pluck('job_id')->toArray())
            ->get()
            ->pluck('name', 'id')
            ->toArray();
    }
    public function toggleOpenModal()
    {
        $this->openModal = !$this->openModal;
        if ($this->openModal) {
            $this->getEmployees();
            $this->getUsers();
            $this->getJobs();
            $this->addApproval();
            $this->types = HrTracker::types();
            $this->statuses = HrTracker::statuses();
        }
    }

    public function getTrackerApprovals()
    {
        if (isset($this->trackers->approvals)) {
            $this->tracker_approvals = $this->trackers->approvals;
        }
    }

    public function create()
    {
        $this->validate([
            'name' => 'required',
            'type' => 'required',
            'status' => 'required',
            'tracker_approvals.*.user_id' => 'required',
            'tracker_approvals.*.sort' => 'required',
        ]);

        $tracker = HrTracker::create([
            'name' => $this->name,
            'type' => $this->type,
            'status' => $this->status,
            'department_id' => $this->department->id,
            'tracker_approvals' => $this->tracker_approvals
        ]);
        $this->reset();
        $this->render();

    }

    public function addApproval()
    {
        $this->tracker_approvals[] = [
            'user_id' => null,
            'sort' => count($this->tracker_approvals ?? []) + 1,
        ];
    }
    public function removeApproval($index)
    {
        unset($this->tracker_approvals[$index]);
    }
}
