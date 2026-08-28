<?php

namespace Modules\HR\App\Livewire\Profile;

use Livewire\Component;
use App\Repositories\EmployeeRepository;
use Modules\HR\App\Repositories\HrEmployeeRepository;
use Modules\HR\App\Models\HrEmployee;
use Modules\HR\App\Models\HrAttendance;
use Modules\HR\App\Repositories\HrAttendanceRepository;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Carbon\Carbon;

class Location extends Component
{
    use LivewireAlert, AuthorizesRequests;

    public $Places;
    public $employee;
    public $HrEmployee;
    public $user;
    public $shiftEmployees;
    public $Attendance;
    protected HrAttendanceRepository $attendRepository;

    public function mount(HrAttendanceRepository $attendRepository)
    {
        $this->attendRepository = $attendRepository;

        $this->user = auth()->id();
        $this->employee = auth()->user()->employee;
        $this->HrEmployee = $this->employee?->hrEmployee;

        $this->Places = $this->locationGroup();

        // Fixed date format and typo in method name
        $currentDate = now()->toDateString();

        $this->Attendance = $this->attendRepository->EmployeePresenceSearch(
            (object) [
                'employee_id' => $this->HrEmployee->id,
                'start_date' => $currentDate,
                'end_date' => $currentDate,
            ],
        );

        $this->shiftEmployees = $this->ShiftEmp();
    }

    public function render()
    {
        // dd( $this->Attendance);
        // Removed dd() for debugging
        return view('hr::livewire.profile.location');
    }

    public function locationGroup()
    {
        if (!$this->HrEmployee || !$this->employee) {
            return collect();
        }

        if ($this->HrEmployee->attendance_type == HrEmployee::ATTENDANCE_GEOGRAPHIC || $this->HrEmployee->attendance_type == HrEmployee::ATTENDANCE_All) {
            return $this->attendRepository->Place($this->HrEmployee->id, $this->HrEmployee->department_id, $this->employee->branch_id ?? null);
        }

        return collect();
    }


    private function ShiftEmp()
    {
        // تأكد أن الـ Shift موجود
        $shifts = $this->HrEmployee?->shift?->shifts ?? collect();

        if ($shifts->isEmpty()) {
            return collect();
        }

        $currentTime = now();
        $currentTimeInSeconds = $currentTime->secondsSinceMidnight();

        $closestShift = null;
        $smallestDiff = PHP_INT_MAX;

        foreach ($shifts as $shift) {
            $fromTimeInSeconds = Carbon::createFromFormat('H:i:s', $shift->from)->secondsSinceMidnight();
            $toTimeInSeconds = Carbon::createFromFormat('H:i:s', $shift->to)->secondsSinceMidnight();

            $diffFromStart = abs($currentTimeInSeconds - $fromTimeInSeconds);
            $diffFromEnd = abs($currentTimeInSeconds - $toTimeInSeconds);

            $minDiff = min($diffFromStart, $diffFromEnd);

            if ($minDiff < $smallestDiff) {
                $smallestDiff = $minDiff;
                $closestShift = $shift;
            }
        }

        // Fixed to use HrEmployee->id for consistency
        $query = HrAttendance::query()
            ->where('employee_id', $this->HrEmployee->id)
            ->where('date', now()->toDateString());

        if ($closestShift) {
            $query->where('shift_from', $closestShift->from)->where('shift_to', '>=', $closestShift->to);
        }

        return $query->get();
    }


    
}
