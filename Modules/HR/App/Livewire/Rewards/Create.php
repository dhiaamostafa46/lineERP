<?php

namespace Modules\HR\App\Livewire\Rewards;

use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Modules\HR\App\Models\HrReward;
use Modules\HR\App\Models\HrSalary;
use Modules\HR\App\Repositories\HrRewardRepository;

class Create extends Component
{
    use LivewireAlert;
    public $reward;
    public $employees;
    public $types;
    public $employee_id;
    public $payroll_id;
    public $type;
    public $amount;
    public $status;
    public $over_time;
    public $days_off;
    public $start_at;
    public $end_at;
    public $note;
    public $due_date;



    protected HrRewardRepository $hrRewardRepository;
    public function boot(HrRewardRepository $hrRewardRepo)
    {
        $this->hrRewardRepository = $hrRewardRepo;
    }

    public function mount()
    {
        $this->employees = $this->hrRewardRepository->employees();
        $this->types = $this->hrRewardRepository->types();
        $this->resetInput();
        $this->setInput();
    }

    public function render()
    {
        return view('hr::livewire.rewards.create');
    }

    public function rules()
    {
        return [
            'employee_id' => 'required',
            'type'        => 'required',
            'amount'      => 'required_if:type,2',
            'over_time'   => 'required_if:type,1',
            'days_off'    => 'required_if:type,3',
            'start_at'    => 'required_if:type,3',
            'end_at'      => 'required_if:type,3',
            'note'        => 'required_if:type,4',
            'due_date'    => 'required_if:type,2',

        ];
    }

    public function updatedType()
    {
        $this->amount      = null;
        $this->status      = null;
        $this->over_time   = null;
        $this->days_off    = null;
        $this->start_at    = null;
        $this->end_at      = null;
    }

    public function saveChanges()
    {


        $this->getAmountOverTime();
        $inputs = $this->validate($this->rules());

        $reward = $this->hrRewardRepository->create($inputs);
        $this->hrRewardRepository->checkTracking($reward);


        $this->alert('success', __('hr::models/hr_rewards.alerts.created'));
        $this->resetInput();
        return $this->redirect(route('hr.rewards.index'));
    }

    public function updateChanges()
    {
        $this->getAmountOverTime();
        $inputs = $this->validate($this->rules());
        $this->hrRewardRepository->update($inputs, $this->reward->id);
        $this->hrRewardRepository->checkTracking($this->reward);
        $this->alert('success', __('messages.updated', ['model' => __('hr::models/hr_rewards.singular')]));
        $this->resetInput();
        return $this->redirect(route('hr.rewards.index'));
    }

    public function resetInput()
    {
        $this->employee_id = null;
        $this->type        = null;
        $this->amount      = null;
        $this->status      = null;
        $this->over_time   = null;
        $this->days_off    = null;
        $this->start_at    = null;
        $this->end_at      = null;
    }

    public function setInput()
    {
        if ($this->reward) {
            $this->employee_id = $this->reward->employee_id;
            $this->type        = $this->reward->type;
            $this->amount      = $this->reward->amount;
            $this->status      = $this->reward->status;
            $this->over_time   = $this->reward->over_time;
            $this->days_off    = $this->reward->days_off;
            $this->start_at    = optional($this->reward->start_at)->format('Y-m-d');
            $this->end_at      = optional($this->reward->end_at)->format('Y-m-d');
            $this->due_date    = optional($this->reward->due_date)->format('Y-m-d');
            $this->note        = $this->reward->note;
        }
    }

    public function getAmountOverTime()
    {
        if ($this->over_time) {
            $salary = HrSalary::where('employee_id', $this->employee_id)->first();
            if ($salary) {
                $basic_salary = (float)$salary->basic;
                $this->amount = round(($basic_salary / 176) * $this->over_time);
            } else {
                $this->amount = 0;
            }
        }
    }
}
