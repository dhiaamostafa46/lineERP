<?php

namespace Modules\HR\App\Livewire\Payrolls\Approvals;

use Livewire\Component;
use Livewire\Attributes\On;
use Modules\HR\App\Models\HrPayrollApproval;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Modules\HR\App\Models\HrPayroll;
use Modules\HR\App\Models\HrSetting;
use Modules\HR\App\Repositories\HrPayrollApprovalRepository;

class Approve extends Component
{
    use LivewireAlert;
    public $approvals_is_ready;
    public $payroll_id;
    public $note;
    public $available = false;
    public $approved = true;
    public $approval;

    protected HrPayrollApprovalRepository $hrApprovalRepo;

    public function boot(HrPayrollApprovalRepository $hrPayrollApprovalRepo)
    {
        $this->hrApprovalRepo = $hrPayrollApprovalRepo;
    }

    #[On('payroll-approvals-updated')]
    public function render()
    {
        $this->checkApproved();
        return view('hr::livewire.payrolls.approvals.approve');
    }

    public function checkApproved()
    {
        $payroll = HrPayroll::find($this->payroll_id);


        if ($payroll->approvals_is_ready) {
            $approval = $this->hrApprovalRepo->allQuery([
                'payroll_id' => $this->payroll_id,
                'user_id'    => auth()->id()
            ])->first();



            if ($approval) {
                $this->available = $approval->is_current;
                if ($approval->status == HrPayrollApproval::STATUS_PENDING) {
                    $this->approved = false;
                } else {
                    $this->approved = true;
                }
                $this->approval = $approval;
                $this->note = $approval->note;
            }
        }
    }

    public function create_approved($approval_id)
    {
        $approval = $this->hrApprovalRepo->find($approval_id);
        if (isset($approval->status) && $approval->status == HrPayrollApproval::STATUS_PENDING) {
            $this->hrApprovalRepo->update([
                'status' => HrPayrollApproval::STATUS_APPROVED,
                'is_current' => 0,
                'note'   => $this->note
            ], $approval_id);

            $next = HrPayrollApproval::where('payroll_id', $this->payroll_id)
                ->where('sort', $approval->sort + 1)
                ->where('id', '!=', $approval_id)
                ->first();

            if ($next) {
                $next->update(['is_current' => 1]);
            }
            $this->hrApprovalRepo->update([
                'status' => HrPayrollApproval::STATUS_APPROVED,
                'is_current' => 0,
                'note'   => $this->note
            ], $approval_id);

            $this->alert('success', __('hr::models/hr_payroll_approvals.fields.approved'));
        } else {
            $this->alert('error', 'Cannot Approve');
        }
        $this->checkApproved();
        $this->checkApprovalsDone();
    }

    public function create_rejected($approval_id)
    {
        $this->validate(['note' => 'required']);
        $approval = $this->hrApprovalRepo->find($approval_id);
        if (isset($approval->status) && $approval->status == HrPayrollApproval::STATUS_PENDING) {
            $this->hrApprovalRepo->update([
                'status' => HrPayrollApproval::STATUS_REJECTED,
                'note'   => $this->note
            ], $approval_id);
            $this->alert('success', __('hr::models/hr_payroll_approvals.fields.rejected'));
        } else {
            $this->alert('error', 'Cannot Reject');
        }
        $this->checkApproved();
        $this->checkApprovalsDone();
    }


    public function checkApprovalsDone()
    {
        $approvals = $this->hrApprovalRepo->allQuery(['payroll_id' => $this->payroll_id])
            ->whereIn('status', [HrPayrollApproval::STATUS_PENDING, HrPayrollApproval::STATUS_REJECTED])
            ->get();
            

        // if ($approvals->count() == 0) {

        //     $this->dispatch(__('hr::models/hr_payrolls.fields.payroll_accredited'));


        // }

        // $this->dispatch(__('hr::models/hr_payrolls.fields.updated'));
        if ($approvals->count() == 0) {
            $this->dispatch('payroll-accredited');
        }

        $this->dispatch('payroll-updated');
    }

    public function back_step($approval_id)
    {
        $this->validate(['note' => 'required']);

        $approvals = $this->hrApprovalRepo->allQuery([
            'payroll_id' => $this->payroll_id
        ])->orderByDesc('sort')->get();

        foreach ($approvals as $approval) {
            if ($approval->id == $approval_id) {
                $this->hrApprovalRepo->update([
                    'status'     => HrPayrollApproval::STATUS_PENDING,
                    'note'       => $this->note,
                    'is_current' => 0
                ], $approval_id);
            } else {
                $this->hrApprovalRepo->update([
                    'status'     => HrPayrollApproval::STATUS_PENDING,
                    'is_current' => 1
                ], $approval->id);
                break;
            }
        }

        $this->dispatch('payroll-updated');
    }

    public function restart($approval_id)
    {
        $this->validate(['note' => 'required']);

        $approvals = $this->hrApprovalRepo->allQuery([
            'payroll_id' => $this->payroll_id
        ])->orderByDesc('sort')->get();

        foreach ($approvals as $approval) {
            if ($approval->id == $approval_id) {
                $this->hrApprovalRepo->update([
                    'status'     => HrPayrollApproval::STATUS_PENDING,
                    'note'       => $this->note,
                    'is_current' => 0
                ], $approval_id);
            } elseif ($approval->sort == 1) {
                $this->hrApprovalRepo->update([
                    'status'     => HrPayrollApproval::STATUS_PENDING,
                    'is_current' => 1
                ], $approval->id);
            } else {
                $this->hrApprovalRepo->update([
                    'status'     => HrPayrollApproval::STATUS_PENDING,
                    'is_current' => 0
                ], $approval->id);
            }
        }

        $this->dispatch('payroll-updated');
    }
}
