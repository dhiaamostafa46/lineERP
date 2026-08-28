<?php

namespace Modules\HR\App\Livewire\Attendance;

use Livewire\Component;
use Modules\HR\App\Models\HrEmployee;
use Modules\HR\App\Jobs\ReprocessAttendanceJob;
use Carbon\Carbon;

class ReprocessModal extends Component
{
    public $start_date;
    public $end_date;
    public $employee_id;
    public $isProcessing = false;
    public $statusMessage = '';

    public function mount()
    {
        $this->start_date = Carbon::now()->startOfMonth()->toDateString();
        $this->end_date = Carbon::now()->yesterday()->toDateString();
    }

    public function rules()
    {
        return [
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'employee_id' => 'nullable|exists:hr_employees,id',
        ];
    }

    public function reprocess()
    {
        $this->validate();

        $this->isProcessing = true;

        ReprocessAttendanceJob::dispatch($this->start_date, $this->end_date, $this->employee_id ? (int)$this->employee_id : null);

        $this->statusMessage = __('hr::models/hr_attendances.reprocess_started') ?? 'تم بدء إعادة المعالجة في الخلفية بنجاح';
        $this->isProcessing = false;

        $this->dispatch('attendanceReprocessStarted');
    }

    public function render()
    {
        $employees = HrEmployee::select('id', 'name')->get();

        return view('hr::livewire.attendance.reprocess-modal', [
            'employees' => $employees,
        ]);
    }
}
