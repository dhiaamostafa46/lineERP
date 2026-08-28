<?php

namespace Modules\HR\App\Livewire\Employees;

use Livewire\Component;
use Livewire\WithPagination;
use App\Exports\HrEmployeesExport;
use Livewire\WithoutUrlPagination;
use Maatwebsite\Excel\Facades\Excel;
use Modules\HR\App\Repositories\HrEmployeeRepository;

class Index extends Component
{
    use WithPagination, WithoutUrlPagination;

    public $username;
    public $job_id;
    public $shift_id;
    public $department_id;
    public $start_filter = false;
    public $branch_id;

    public int $pagination        = 10;
    public array $jobs            = [];
    public array $departments     = [];
    public array $shifts          = [];
    public array $genders         = [];
    public array $maritalStatuses = [];
    public array $identityTypes   = [];
    public array $user_roles      = [];
    public array $user_statuses   = [];
     public array $branchs            = [];

    private HrEmployeeRepository $hrEmployeeRepository;

    public function boot(HrEmployeeRepository $hrEmployeeRepo)
    {
        $this->hrEmployeeRepository = $hrEmployeeRepo;
    }

    public function mount()
    {
        $this->getLists();
    }

    public function toggleFilter()
    {
        $this->start_filter = !$this->start_filter;
    }

    public function updated() {}

    public function filter()
    {
        $this->render();
    }

    public function resetInputs()
    {
        $this->job_id        = null;
        $this->shift_id      = null;
        $this->department_id = null;
        $this->username      = null;
        $this->branch_id      = null;
        $this->pagination    = 5;
    }

    public function render()
    {
        $data['employees'] = $this->getEmployees();
        return view('hr::livewire.employees.index', $data);
    }

    public function getEmployees()
    {
        return $this->hrEmployeeRepository
            ->allQuery([])
            ->when($this->job_id, function ($q) {
                return $q->where('job_id', $this->job_id);
            })
            ->when($this->shift_id, function ($q) {
                return $q->where('shift_id', $this->shift_id);
            })
            ->when($this->department_id, function ($q) {
                return $q->where('department_id', $this->department_id);
            })
            ->when($this->username, function ($q) {
                return $q->whereRelation('main_employee', 'username', 'like', '%' . $this->username . '%');
            })

            ->when($this->branch_id, function ($q) {
                return $q->whereRelation('main_employee', 'branch_id', 'like', '%' . $this->branch_id . '%');
            })
            ->select(['id','job_number', 'username','job_id', 'department_id', 'shift_id', 'employee_id', 'job_level', 'specialty', 'license_expired_at'])
            ->with(['job:id', 'job.translations', 'department:id', 'department.translations', 'shift:id', 'shift.translations','main_employee.Branch:id', 'main_employee.Branch.translations', 'main_employee:id,full_name,phone,branch_id', 'main_employee.identity:employee_id,identity_expired_at'])
            ->orderBy('id', 'desc')
            ->paginate($this->pagination ?? 5);

    }

    public function getLists()
    {
        $this->jobs            = $this->hrEmployeeRepository->jobs();
        $this->departments     = $this->hrEmployeeRepository->departments();
        $this->shifts          = $this->hrEmployeeRepository->shifts();
        $this->genders         = $this->hrEmployeeRepository->genders();
        $this->maritalStatuses = $this->hrEmployeeRepository->maritalStatuses();
        $this->identityTypes   = $this->hrEmployeeRepository->identityTypes();
        $this->user_roles      = $this->hrEmployeeRepository->user_roles();
        $this->user_statuses   = $this->hrEmployeeRepository->user_statuses();
        $this->branchs        = $this->hrEmployeeRepository->branchs();
    }

    public function custom_export()
    {
        $employees = $this->hrEmployeeRepository
            ->allQuery([])
            ->when($this->job_id, function ($q) {
                return $q->where('job_id', $this->job_id);
            })
            ->when($this->shift_id, function ($q) {
                return $q->where('shift_id', $this->shift_id);
            })
            ->when($this->department_id, function ($q) {
                return $q->where('department_id', $this->department_id);
            })
            ->when($this->username, function ($q) {
                return $q->whereRelation('main_employee', 'username', 'like', '%' . $this->username . '%');
            })
            ->with(['job', 'job.translations', 'department', 'department.translations', 'shift', 'shift.translations', 'main_employee', 'main_employee.identity'])
            ->get();

        return Excel::download(new HrEmployeesExport($employees), 'employees.xlsx');
    }
}
