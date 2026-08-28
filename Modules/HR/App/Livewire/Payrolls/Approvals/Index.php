<?php

namespace Modules\HR\App\Livewire\Payrolls\Approvals;

use Livewire\Component;
use Livewire\Attributes\On;
use Modules\HR\App\Livewire\Payrolls\Show;
use Modules\HR\App\Models\HrPayrollApproval;
use Modules\HR\App\Livewire\Payrolls\Approvals\Approve;
use Modules\HR\App\Models\HrPayroll;
use Modules\HR\App\Repositories\HrPayrollApprovalRepository;

class Index extends Component
{
    public $approvals_is_ready;
    public $payroll_id;
    public $user_id;
    public $without_user_id;
    public $users = [];
    public $approvals = [];

    protected HrPayrollApprovalRepository $hrApprovalRepo;

    public function boot(HrPayrollApprovalRepository $hrPayrollApprovalRepo)
    {
        $this->hrApprovalRepo = $hrPayrollApprovalRepo;
    }

    public function render()
    {
        $this->getApprovals();
        return view('hr::livewire.payrolls.approvals.index');
    }

    public function create()
    {
        $sort = count($this->approvals) + 1;
        $this->hrApprovalRepo->create([
            'payroll_id' => $this->payroll_id,
            'user_id' => $this->user_id,
            'status' => HrPayrollApproval::STATUS_PENDING,
            'sort' => $sort,
            'is_current' => $sort == 1 ? 1 : 0
        ]);
        $this->dispatch('payroll-updated');
        $this->render();
    }

    public function delete($id)
    {
        $approval = $this->hrApprovalRepo->find($id);
        $next = HrPayrollApproval::where('payroll_id', $approval->payroll_id)
            ->where('sort', '>', $approval->sort)
            ->first();
        if ($next && $approval->is_current == 1) {
            $next->update([
                'sort' => $approval->sort,
                'is_current' => 1
            ]);
        }
        $this->hrApprovalRepo->delete($id);
        $this->dispatch('payroll-updated')->to(Show::class);
        $this->render();
    }

    public function getApprovals()
    {
        $query = ['payroll_id' => $this->payroll_id];
        $approvals = $this->hrApprovalRepo->allQuery($query)->orderBy('sort')->get();
        $this->approvals = $approvals;
        $this->users = $this->hrApprovalRepo->users($approvals->pluck('user_id')->toArray());
    }

    public function resetInputs()
    {
        $this->user_id = null;
    }

    public function updateApprovalOrder($params)
    {
        foreach ($params as $item) {
            $this->hrApprovalRepo->update([
                'sort' => (int)$item['order'],
                'status' => HrPayrollApproval::STATUS_PENDING,
                'is_current' => $item['order'] == 1 ? 1 : 0
            ], $item['value']);
        }
        $this->getApprovals();
        $this->dispatch('payroll-approvals-updated')->to(Approve::class);
    }

    public function approvalsReady()
    {
        HrPayroll::where('id', $this->payroll_id)->update(['approvals_is_ready' => 1]);
        $this->dispatch('payroll-updated')->to(Show::class);
        $this->dispatch('payroll-approvals-updated')->to(Approve::class);
        $this->approvals_is_ready = 1;
    }
}
