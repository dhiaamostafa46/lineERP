<?php

namespace Modules\HR\App\Livewire\Payrolls\Employees;

use Livewire\Component;
use Livewire\Attributes\On;
use Modules\HR\App\Models\HrPayrollEmployee;
use Modules\HR\App\Models\HrPayrollTransaction;
use Modules\HR\App\Repositories\HrPayrollEmployeeRepository;
use Modules\HR\App\Repositories\HrPayrollTransactionRepository;

class Show extends Component
{
    public $openModal = false;
    public $employee;
    public $employee_id;
    public $query;
    public $transactions = [];
    public $note;

    private HrPayrollTransactionRepository $transactionRepo;
    private HrPayrollEmployeeRepository $employeeRepo;

    public function boot(HrPayrollTransactionRepository $hrPayrollTransactionRepo, HrPayrollEmployeeRepository $hrPayrollEmployeeRepo)
    {
        $this->transactionRepo = $hrPayrollTransactionRepo;
        $this->employeeRepo = $hrPayrollEmployeeRepo;
    }

    #[On('open-modal')]
    public function openModal($employee_id)
    {
        $this->employee_id = $employee_id;
        $this->query = ['payroll_employee_id' => $employee_id];
        $this->getTransactions();
        $this->getEmployee();
        $this->openModal = !$this->openModal;
    }

    public function render()
    {
        return view('hr::livewire.payrolls.employees.show');
    }

    public function closeModal()
    {
        $this->openModal = !$this->openModal;
        $this->employee_id = null;
        $this->query = [];
    }

    public function getEmployee()
    {
        $this->employee = $this->employeeRepo->find($this->employee_id);
    }
    public function getTransactions()
    {
        $this->transactions = $this->transactionRepo->allQuery($this->query)->get();
    }

    public function updateNote($transaction_id)
    {
        $this->transactionRepo->update(['note' => $this->note], $transaction_id);
        $this->note = null;
        $this->getTransactions();
    }

    public function approveAll()
    {
        $employee = $this->employeeRepo->update(['status' => HrPayrollEmployee::STATUS_APPROVED], $this->employee_id, false);
        $employee->transactions()->update(['status' => HrPayrollTransaction::STATUS_APPROVED]);
        activity()
            ->causedBy(auth()->user())
            ->on($employee)
            ->event('approved')
            ->log(__('hr::models/hr_payroll_employees.fields.Approved_All_EmpTransaction'));
        $this->getTransactions();
    }

    public function rejectAll()
    {
        $employee = $this->employeeRepo->update(['status' => HrPayrollEmployee::STATUS_REJECTED], $this->employee_id, false);
        $employee->transactions()->update(['status' => HrPayrollTransaction::STATUS_REJECTED]);
        activity()
            ->causedBy(auth()->user())
            ->on($employee)
            ->event('rejected')
            ->log(__('hr::models/hr_payroll_employees.fields.Reject_All_EmpTransaction'));
        $this->getTransactions();
    }

    public function approve($transaction_id)
    {
        $this->transactionRepo->update(['status' => HrPayrollTransaction::STATUS_APPROVED], $transaction_id);
        $this->getTransactions();
    }

    public function reject($transaction_id)
    {
        $this->transactionRepo->update(['status' => HrPayrollTransaction::STATUS_REJECTED], $transaction_id);
        $this->getTransactions();
    }
}
