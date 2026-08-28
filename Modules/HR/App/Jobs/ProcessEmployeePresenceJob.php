<?php

namespace Modules\HR\App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\HR\App\Models\HrAttendance;
use Modules\HR\App\Models\HrEmployee;
use Modules\HR\App\Models\HrHoliday;
use Modules\HR\App\Models\HrJustification;
use Modules\HR\App\Models\HrTimeTrack;
use Modules\HR\App\Models\HrTimeTrackDetails;
use Modules\HR\App\Models\HrCalendarEvents;
use Modules\HR\App\Models\HrSetting;
use Illuminate\Support\Collection;

class ProcessEmployeePresenceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $date;

    /**
     * Create a new job instance.
     */
    public function __construct(?string $date = null)
    {
        $this->date = $date ? Carbon::parse($date)->toDateString() : Carbon::now()->toDateString();
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $dateObject = Carbon::parse($this->date);
        $today = $dateObject->toDateString();
        $currentDay = strtolower($dateObject->format('l'));

        Log::info("🔹 [Job] Starting daily attendance processing for date: {$today}");

        $employees = HrEmployee::with('shift.shifts')->get();
        if ($employees->isEmpty()) {
            Log::info("No employees found to process.");
            return;
        }

        $allAttendance = $this->fetchAllAttendanceForDate($today);
        $holidays = $this->fetchAllHolidaysForDate($today);
        $isOfficialHoliday = $this->isOfficialHoliday($today);
        $allJustifications = $this->fetchAllJustificationsForDate($today);

        DB::transaction(function () use ($employees, $today, $currentDay, $allAttendance, $holidays, $isOfficialHoliday, $allJustifications) {
            foreach ($employees as $employee) {
                list($timeTrackData, $timeTrackDetailsData) = $this->prepareEmployeeData(
                    $employee,
                    $today,
                    $currentDay,
                    $allAttendance->get($employee->id, collect()),
                    $holidays,
                    $isOfficialHoliday,
                    $allJustifications->get($employee->id, collect())
                );

                $timeTrack = HrTimeTrack::updateOrCreate(
                    ['employee_id' => $employee->id, 'date' => $today],
                    $timeTrackData
                );

                HrTimeTrackDetails::where('hr_time_track_id', $timeTrack->id)->delete();

                if (!empty($timeTrackDetailsData)) {
                    $detailsToInsert = array_map(function ($detail) use ($timeTrack) {
                        $detail['hr_time_track_id'] = $timeTrack->id;
                        return $detail;
                    }, $timeTrackDetailsData);
                    HrTimeTrackDetails::insert($detailsToInsert);
                }
            }
        });

        Log::info("🔹 [Job] Finished daily attendance processing for date: {$today}");
    }

    private function prepareEmployeeData(HrEmployee $employee, string $today, string $currentDay, Collection $employeeAttendance, array $holidays, bool $isOfficialHoliday, Collection $employeeJustifications): array
    {
        $shift = $employee->shift;
        $shifts = $shift ? $shift->shifts : collect();

        $attendanceCollection = $this->buildAttendanceCollectionForEmployee($employee, $today, $shifts, $employeeAttendance);

        $detailTypes = $attendanceCollection->map(function ($attendance) use ($employeeJustifications, $shifts) {
            return $this->getDetailType($attendance, $employeeJustifications, $shifts);
        });

        $isWorkDay = $shift && is_array($shift->work_days) && in_array($currentDay, $shift->work_days);
        $isExemptDay = $shift && is_array($shift->exempt_days) && in_array($currentDay, $shift->exempt_days);
        $isPersonalHoliday = isset($holidays['personal'][$employee->id]) || $holidays['general']->isNotEmpty();

        $hasPresence = $detailTypes->contains(HrTimeTrackDetails::TYPE_PRESENT);
        $hasJustification = $detailTypes->contains(HrTimeTrackDetails::TYPE_JUSTIFICATION);
        $hasMissingPunch = $detailTypes->contains(HrTimeTrackDetails::TYPE_FINGERPRINT);

        if ($isOfficialHoliday) {
            $type = HrTimeTrack::TYPE_OFFICIAL_HOLIDAY;
        } elseif ($isPersonalHoliday) {
            $type = HrTimeTrack::TYPE_HOLIDAY;
        } elseif ($isExemptDay) {
            $type = HrTimeTrack::TYPE_EXEMPT;
        } elseif (!$isWorkDay) {
            $type = HrTimeTrack::TYPE_WEEKEND;
        } elseif ($hasJustification && !$hasPresence) {
            $type = HrTimeTrack::TYPE_PRESENT;
        } elseif ($hasMissingPunch) {
            $setting = hr_setting();
            if ($setting->calculate_missing_fingerprint && $setting->missing_fingerprint_policy == 2) {
                $type = HrTimeTrack::TYPE_ABSENT;
            } else {
                $type = HrTimeTrack::TYPE_PRESENT;
            }
        } elseif ($hasPresence) {
            $type = HrTimeTrack::TYPE_PRESENT;
        } else {
            $type = HrTimeTrack::TYPE_ABSENT;
        }

        $timeTrackData = [
            'day' => $currentDay,
            'status' => 1,
            'type' => $type,
            'hour' => $shift->work_hours ?? 0,
        ];

        $timeTrackDetails = [];
        foreach ($attendanceCollection as $index => $attendance) {
            $timeTrackDetails[] = [
                'check_time' => $attendance->first_check_in,
                'check_out' => $attendance->last_check_out,
                'shift_from' => $attendance->shift_from,
                'shift_to' => $attendance->shift_to,
                'delay' => $attendance->min_delay ?? 0,
                'early_arrival' => $attendance->early_arrival ?? 0,
                'early_leave' => $attendance->min_early_leave ?? 0,
                'overtime' => $attendance->max_overtime ?? 0,
                'total_work_seconds' => $attendance->total_work_seconds ?? 0,
                'lat' => $attendance->lat,
                'lon' => $attendance->lon,
                'address' => $attendance->address,
                'type' => $detailTypes[$index],
            ];
        }

        return [$timeTrackData, $timeTrackDetails];
    }

    private function getDetailType($attendance, Collection $justifications, Collection $shifts): int
    {
        $shiftModel = $shifts->firstWhere('from', $attendance->shift_from);
        if ($justifications->contains(fn ($j) => $j->shift_id === ($shiftModel->id ?? null))) {
            return HrTimeTrackDetails::TYPE_JUSTIFICATION;
        }

        if (!is_null($attendance->first_check_in) && !is_null($attendance->last_check_out)) {
            return HrTimeTrackDetails::TYPE_PRESENT;
        }

        if (!is_null($attendance->first_check_in) || !is_null($attendance->last_check_out)) {
            return HrTimeTrackDetails::TYPE_FINGERPRINT;
        }

        return HrTimeTrackDetails::TYPE_ABSENT;
    }

    private function buildAttendanceCollectionForEmployee(HrEmployee $employee, string $today, Collection $shifts, ?Collection $employeeAttendance): Collection
    {
        $results = collect();
        $employeeAttendance = $employeeAttendance ?? collect();

        if ($shifts->isNotEmpty()) {
            foreach ($shifts as $shift) {
                $attendanceData = $employeeAttendance->first(fn ($att) => $att->shift_from === $shift->from && $att->shift_to === $shift->to);

                if ($attendanceData) {
                    $results->push($attendanceData);
                } else {
                    $results->push((object)[
                        'date' => $today, 'employee_id' => $employee->id, 'address' => null,
                        'shift_from' => $shift->from, 'shift_to' => $shift->to, 'type' => null,
                        'lat' => null, 'lon' => null, 'first_check_in' => null, 'last_check_out' => null,
                        'earlyArrival' => null, 'min_delay' => null, 'min_early_leave' => null,
                        'max_overtime' => null, 'total_work_seconds' => 0,
                    ]);
                }
            }
        } elseif ($employeeAttendance->isNotEmpty()) {
            $results = $results->merge($employeeAttendance);
        }

        return $results;
    }

    private function fetchAllAttendanceForDate(string $today): Collection
    {
        $rawAttendance = HrAttendance::select(
            'date', 'employee_id', 'shift_from', 'shift_to',
            DB::raw('MIN(CASE WHEN kind = 1 THEN check_time END) as first_check_in'),
            DB::raw('MAX(CASE WHEN kind = 2 THEN check_time END) as last_check_out'),
            DB::raw('MAX(CASE WHEN kind = 1 THEN early_arrival END) as early_arrival'),
            DB::raw('MIN(CASE WHEN kind = 1 THEN delay END) as min_delay'),
            DB::raw('MIN(CASE WHEN kind = 2 THEN early_leave END) as min_early_leave'),
            DB::raw('MAX(CASE WHEN kind = 2 THEN overtime END) as max_overtime'),
            DB::raw('TIMESTAMPDIFF(SECOND, MIN(CASE WHEN kind = 1 THEN check_time END), MAX(CASE WHEN kind = 2 THEN check_time END)) as total_work_seconds'),
            DB::raw('SUBSTRING_INDEX(GROUP_CONCAT(address ORDER BY check_time), ",", 1) as address'),
            DB::raw('SUBSTRING_INDEX(GROUP_CONCAT(lat ORDER BY check_time), ",", 1) as lat'),
            DB::raw('SUBSTRING_INDEX(GROUP_CONCAT(lon ORDER BY check_time), ",", 1) as lon'),
            DB::raw('SUBSTRING_INDEX(GROUP_CONCAT(type ORDER BY check_time), ",", 1) as type')
        )
        ->where('date', $today)
        ->groupBy('date', 'employee_id', 'shift_from', 'shift_to')
        ->get();

        return $rawAttendance->groupBy('employee_id');
    }

    private function fetchAllHolidaysForDate(string $today): array
    {
        $holidays = HrHoliday::where('from_at', '<=', $today)
            ->where('end_at', '>=', $today)
            ->where('status', 2)
            ->get();

        return [
            'personal' => $holidays->whereNotNull('employee_id')->groupBy('employee_id'),
            'general' => $holidays->whereNull('employee_id'),
        ];
    }

    private function isOfficialHoliday(string $today): bool
    {
        return HrCalendarEvents::activeOnly()
            ->whereRaw("JSON_EXTRACT(rules, ?) IS NOT NULL", ['$.'.$today])
            ->exists();
    }

    private function fetchAllJustificationsForDate(string $today): Collection
    {
        return HrJustification::where('status', HrJustification::STATUS_APPROVED)
            ->whereDate('request_date', $today)
            ->get()
            ->groupBy('employee_id');
    }
}
