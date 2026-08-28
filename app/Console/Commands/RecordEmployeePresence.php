<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
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
use Modules\HR\App\Jobs\ProcessEmployeePresenceJob;
use Illuminate\Support\Collection;

class RecordEmployeePresence extends Command
{
    protected $signature = 'attendance:record {--date= : The date to process in Y-m-d format. Defaults to today.}';
    protected $description = 'Generate and record daily employee attendance (Optimized)';

    protected $missingPunchPolicy;

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dateObject = $this->option('date') ? Carbon::parse($this->option('date')) : Carbon::now();
        $today = $dateObject->toDateString();

        Log::info("🔹 Dispatching daily attendance processing job for: {$today}");
        $this->info("Dispatching attendance processing job for {$today}...");

        ProcessEmployeePresenceJob::dispatchSync($today);

        Log::info("🔹 Finished daily attendance processing for: {$today}");
        $this->info("Successfully processed attendance for {$today}.");
    }

    /**
     * Prepare attendance data for a single employee using pre-fetched data.
     */
    private function prepareEmployeeData(HrEmployee $employee, string $today,  string $currentDay, Collection $employeeAttendance, array $holidays, bool $isOfficialHoliday,  Collection $employeeJustifications): array
    {
        $shift = $employee->shift;
        $shifts = $shift ? $shift->shifts : collect();

        // Combine raw attendance with empty records for shifts without attendance
        $attendanceCollection = $this->buildAttendanceCollectionForEmployee($employee, $today, $shifts, $employeeAttendance);

        // Get the types of all detail records first to determine the overall day status
        $detailTypes = $attendanceCollection->map(function ($attendance) use ($employeeJustifications, $shifts) {
            return $this->getDetailType($attendance, $employeeJustifications, $shifts);
        });

        // --- Determine day-level status flags ---
        $isWorkDay = $shift && is_array($shift->work_days) && in_array($currentDay, $shift->work_days);
        $isExemptDay = $shift && is_array($shift->exempt_days) && in_array($currentDay, $shift->exempt_days);
        $isPersonalHoliday = isset($holidays['personal'][$employee->id]) || $holidays['general']->isNotEmpty();

        $hasPresence = $detailTypes->contains(HrTimeTrackDetails::TYPE_PRESENT);
        $hasJustification = $detailTypes->contains(HrTimeTrackDetails::TYPE_JUSTIFICATION);
        $hasMissingPunch = $detailTypes->contains(HrTimeTrackDetails::TYPE_FINGERPRINT);

        // --- Determine final HrTimeTrack type based on priority ---

        // 1. Highest priority: Holidays and non-workdays
        if ($isOfficialHoliday) {
            $type = HrTimeTrack::TYPE_OFFICIAL_HOLIDAY;
        } elseif ($isPersonalHoliday) {
            $type = HrTimeTrack::TYPE_HOLIDAY;
        } elseif ($isExemptDay) {
            $type = HrTimeTrack::TYPE_EXEMPT;
        } elseif (!$isWorkDay) {
            $type = HrTimeTrack::TYPE_WEEKEND;
        }
        // 2. Justification (if any detail is justified, day is not considered a punishable absence)
        elseif ($hasJustification && !$hasPresence) {
            $type = HrTimeTrack::TYPE_PRESENT; // Treat justified absence as 'Present' at the day level
        }
        // 3. Missing Punch Policy
        elseif ($hasMissingPunch) {
            $setting = hr_setting();
            if ($setting->calculate_missing_fingerprint && $setting->missing_fingerprint_policy == 2) { // MISSING_FP_FULL_DAY
                $type = HrTimeTrack::TYPE_ABSENT;
            } else {
                // For other policies (e.g., ignore), treat as present if at least one punch exists.
                $type = HrTimeTrack::TYPE_PRESENT;
            }
        }
        // 4. Presence
        elseif ($hasPresence) {
            $type = HrTimeTrack::TYPE_PRESENT;
        }
        // 5. Absence (default for a workday with no other status)
        else {
            $type = HrTimeTrack::TYPE_ABSENT;
        }

        // Data for the main track record
        $timeTrackData = [
            'day' => $currentDay,
            'status' => 1,
            'type' => $type,
            'hour' => $shift->work_hours ?? 0,
        ];

        // Prepare details data
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
                'type' => $detailTypes[$index], // Use the pre-calculated type
            ];
        }

        return [$timeTrackData, $timeTrackDetails];
    }

    /**
     * Determine the type for an HrTimeTrackDetails record based on a clear priority.
     */
    private function getDetailType($attendance, Collection $justifications, Collection $shifts): int
    {
        // 1. Justification
        $shiftModel = $shifts->firstWhere('from', $attendance->shift_from);
        if ($justifications->contains(fn ($j) => $j->shift_id === ($shiftModel->id ?? null))) {
            return HrTimeTrackDetails::TYPE_JUSTIFICATION;
        }

        // 2. Presence (both punches exist)
        if (!is_null($attendance->first_check_in) && !is_null($attendance->last_check_out)) {
            return HrTimeTrackDetails::TYPE_PRESENT;
        }

        // 3. Missing Punch (one punch exists)
        if (!is_null($attendance->first_check_in) || !is_null($attendance->last_check_out)) {
            return HrTimeTrackDetails::TYPE_FINGERPRINT;
        }

        // 4. Absence (no punches for this shift)
        return HrTimeTrackDetails::TYPE_ABSENT;
    }

    /**
     * Builds a complete attendance collection for an employee, ensuring all shifts are represented.
     */
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
                    // Add an empty record for shifts with no attendance data
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
            // Push any attendance data found for employees without formal shifts
            $results = $results->merge($employeeAttendance);
        }

        return $results;
    }

    // --------------------------------------------------------------------
    // Bulk Data Fetching Methods
    // --------------------------------------------------------------------

    private function fetchAllEmployeesWithShifts(): Collection
    {
        return HrEmployee::with('shift.shifts')->get();
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
            ->where('status', 2) // Approved
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
