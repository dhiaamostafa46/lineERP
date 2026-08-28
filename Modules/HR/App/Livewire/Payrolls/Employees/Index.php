<?php

namespace Modules\HR\App\Livewire\Payrolls\Employees;

use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use Livewire\WithoutUrlPagination;
use Modules\HR\App\Models\HrPayrollEmployee;
use Modules\HR\App\Models\HrPayrollTransaction;
use Modules\HR\App\Repositories\HrPayrollEmployeeRepository;

class Index extends Component
{
    use WithPagination, WithoutUrlPagination;

    public $payroll_id;
    public $query;
    public $search;
    public int $current_paginate_page;
    private HrPayrollEmployeeRepository $employeeRepo;

    public function boot(HrPayrollEmployeeRepository $hrPayrollEmployeeRepo)
    {
        $this->employeeRepo = $hrPayrollEmployeeRepo;
    }

    public function mount($payroll_id)
    {
        $this->payroll_id = $payroll_id;
        $this->query = ['payroll_id' => $this->payroll_id];
    }
    #[On('employees-updated')]
    public function render()
    {
        $search = $this->search;
        $data['payroll_employees'] = $this->employeeRepo->allQuery($this->query)
            ->when($search, function ($q) use ($search) {
                $q->where(function ($query) use ($search) {
                    $items = explode(',', $search);
                    foreach ($items as $item) {
                        $trimmedItem = trim($item);
                        if (!empty($trimmedItem)) {
                            $query->orWhere('username', 'like', '%' . $trimmedItem . '%')
                                ->orWhere('job_name', 'like', '%' . $trimmedItem . '%')
                                ->orWhere('department_name', 'like', '%' . $trimmedItem . '%');
                        }
                    }
                });
                return $q;
            })
            ->paginate(20);
        return view('hr::livewire.payrolls.employees.index', $data);
    }

    public function show($id)
    {
        $this->dispatch('open-modal', employee_id: $id);
    }

    #[On('payroll-employee-approved-all')]
    public function approveAll()
    {
        $employees = $this->employeeRepo->allQuery($this->query)->get();
        foreach ($employees as  $employee) {
            $this->employeeRepo->update(['status' => HrPayrollEmployee::STATUS_APPROVED], $employee->id, false);
            $employee->transactions()->update(['status' => HrPayrollTransaction::STATUS_APPROVED]);
        }
        activity()
            ->causedBy(auth()->user())
            ->on($employees->first()->payroll)
            ->event('Approved All Employees')
            ->log(__('hr::models/hr_payroll_employees.fields.Approved_All_Employees'));
        $this->render();
    }
    #[On('payroll-employee-rejected-all')]
    public function rejectAll()
    {
        $employees = $this->employeeRepo->allQuery($this->query)->get();
        activity()
            ->causedBy(auth()->user())
            ->on($employees->first()->payroll)
            ->event('Reject All Employees')
            ->log(__('hr::models/hr_payroll_employees.fields.Reject_All_Employees'));
        foreach ($employees as  $employee) {
            $this->employeeRepo->update(['status' => HrPayrollEmployee::STATUS_REJECTED], $employee->id, false);
            $employee->transactions()->update(['status' => HrPayrollTransaction::STATUS_REJECTED]);
        }
        $this->render();
    }

    public function searching()
    {
        $this->current_paginate_page = $this->getPage() ??1;

        $this->resetPage();
        $this->render();
    }

    public function clearSearch()
    {
        $this->search = null;
        $this->setPage($this->current_paginate_page);
        $this->render();
    }
}
