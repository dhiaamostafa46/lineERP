<?php

namespace Modules\HR\App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\HR\App\Models\HrAttendancePolicy;
use Modules\HR\App\Models\HrAttendancePolicyLog;
use Modules\HR\App\Models\HrEmployee;
use Modules\HR\App\Models\HrPenalty;
use Modules\HR\App\Models\HrReward;
use Modules\HR\App\Models\HrTimeTrack;
use Modules\HR\App\Models\HrTimeTrackDetails;
use Illuminate\Support\Collection;
use Modules\HR\App\Models\HrSetting;

class AttendancePolicyService
{
    public function processPolicyForEmployees(\Illuminate\Database\Eloquent\Collection $employees, HrAttendancePolicy $policy, Carbon $date): array
    {
        $successCount = 0;
        $errorCount = 0;
        $employeeIds = $employees->pluck('id');

        if ($employeeIds->isEmpty()) {
            return ['success_count' => 0, 'error_count' => 0];
        }

        // Delete existing records for this policy type to ensure idempotency.
        $this->deleteExistingRecords($policy, $employeeIds, $date);

        // 1. Fetch TimeTracks for all employees in the collection
        $timeTracks = HrTimeTrack::with('timeTrackDetails')->whereIn('employee_id', $employeeIds)->where('date', $date->toDateString())->get()->keyBy('employee_id');

        $setting = hr_setting();

        // 2. Prepare historical data in batches
        $policyData = [];
        switch ($policy->type) {
            case HrAttendancePolicy::TYPE_ABSENCE:
                $policyData = $this->prepareAbsenceData($employeeIds, $date);
                break;
            case HrAttendancePolicy::TYPE_LATE:
                $policyData = $this->prepareLateData($employeeIds, $date);
                break;
        }

        // 3. Process each employee
        foreach ($employees as $employee) {
            try {
                $timeTrack = $timeTracks->get($employee->id);
                if (!$timeTrack) {
                    continue;
                }

                if (!$employee->salary) {
                    throw new \Exception('No salary information found');
                }

                $processed = DB::transaction(function () use ($employee, $policy, $timeTrack, $date, $policyData) {
                    switch ($policy->type) {
                        case HrAttendancePolicy::TYPE_ABSENCE:
                            return $this->processAbsencePolicy($employee, $policy, $timeTrack, $date, $policyData);
                        case HrAttendancePolicy::TYPE_LATE:
                            return $this->processLatePolicy($employee, $policy, $timeTrack, $date, $policyData);
                        case HrAttendancePolicy::TYPE_OVERTIME:
                            return $this->processOvertimePolicy($employee, $policy, $timeTrack, $date);
                        default:
                            return false;
                    }
                });

                if ($setting->calculate_missing_fingerprint) {
                    $this->processmissingfingerprint($employee, $timeTrack, $date, $policyData, $setting, $policy);
                }
            } catch (\Exception $e) {
                $errorCount++;
            }
        }

        return ['success_count' => $successCount, 'error_count' => $errorCount];
    }

    private function processmissingfingerprint(HrEmployee $employee, HrTimeTrack $timeTrack, Carbon $date, array $policyData, $setting, HrAttendancePolicy $policy): bool
    {
        // First, delete any existing missing fingerprint penalties for this employee on this day to ensure idempotency.
        HrPenalty::where('employee_id', $employee->id)
            ->whereDate('due_date', $date)
            ->where('type', HrPenalty::TYPE_MISSING_FINGERPRINT)
            ->forceDelete();

        $amount = 0;
        $processed = false;

        foreach ($timeTrack->timeTrackDetails->where('type', HrTimeTrackDetails::TYPE_FINGERPRINT) as $detail) {

           // dd($detail ,$setting->missing_fingerprint_policy);
            // Assumption: 'check_in' and 'check_out' fields exist on HrTimeTrackDetails and are null if a fingerprint is missing.

                $description = '';
                switch ($setting->missing_fingerprint_policy) {
                    case HrSetting::MISSING_FP_FULL_DAY:
                        $amount = $this->calculateAbsencePenalty(1, $policy, $employee);
                        $description = "خصم يوم كامل بسبب بصمة ناقصة";
                        break;
                    case HrSetting::MISSING_FP_HALF_DAY:

                        $amount = $this->calculateAbsencePenalty(0.5, $policy, $employee);
                        $description = "خصم نصف يوم بسبب بصمة ناقصة";

                        break;
                    case HrSetting::MISSING_FP_FULL_SHIFT:
                        $amount = $this->calculateShiftPenalty($detail, $policy, $employee, 1, $setting);
                        $description = "خصم كامل الوردية بسبب بصمة ناقصة";
                        break;
                    case HrSetting::MISSING_FP_HALF_SHIFT:
                        $amount = $this->calculateShiftPenalty($detail, $policy, $employee, 0.5, $setting);
                        $description = "خصم نصف الوردية بسبب بصمة ناقصة";
                        break;
                    default:
                        continue 2; // continue outer loop   
                }

                if ($amount > 0) {
                    HrPenalty::create([
                        'employee_id' => $employee->id,
                        'description' => $description . " لتاريخ {$date->toDateString()}",
                        'amount' => round($amount, 2),
                        'due_date' => $date,
                        'status' => HrPenalty::STATUS_APPROVED,
                        'type' => HrPenalty::TYPE_MISSING_FINGERPRINT,
                    ]);
                    Log::info("Applied missing fingerprint penalty for {$employee->name}: " . round($amount, 2) . " SAR");
                    $processed = true;
                }
                // Process only the first missing fingerprint found for the day.
                break;

        }
        return $processed;
    }

    private function calculateShiftPenalty($detail, HrAttendancePolicy $policy, HrEmployee $employee, float $multiplier, $setting): float
    {
        $salary = $employee->salary;
        if (!$salary) {
            return 0;
        }

        $shiftStart = Carbon::parse($detail->shift_from);
        $shiftEnd = Carbon::parse($detail->shift_to);
        if ($shiftEnd->lt($shiftStart)) {
            $shiftEnd->addDay();
        }
        $shiftDurationInHours = $shiftEnd->diffInSeconds($shiftStart) / 3600;
        if ($shiftDurationInHours <= 0) {
            return 0;
        }

        // Calculate policy-based daily amount, similar to calculateAbsencePenalty
        $baseDayAmount = 0;
        if ($policy->salary_effect_basic) {
            $baseDayAmount += $salary->basic / 30;
        }
        $baseDayAmount += $this->calculateAllowancesAmount($policy, $employee, 30);

        if ($baseDayAmount <= 0) {
            return 0;
        }

        // Get work hours per day from settings to calculate hourly rate
        $workHoursPerDay = $setting->work_hours_per_day ?? 8;
        $hourAmount = $baseDayAmount / $workHoursPerDay;

        return round($shiftDurationInHours * $hourAmount * $multiplier, 2);
    }

    private function deleteExistingRecords(HrAttendancePolicy $policy, Collection $employeeIds, Carbon $date): void
    {
        $penaltyTypes = [
            HrAttendancePolicy::TYPE_ABSENCE => HrPenalty::TYPE_ABSENCE,
            HrAttendancePolicy::TYPE_LATE => HrPenalty::TYPE_LATE,
        ];

        if (array_key_exists($policy->type, $penaltyTypes)) {
            HrPenalty::whereIn('employee_id', $employeeIds)
                ->whereDate('due_date', $date)
                ->where('type', $penaltyTypes[$policy->type])
                ->forceDelete();
        } elseif ($policy->type === HrAttendancePolicy::TYPE_OVERTIME) {
            HrReward::whereIn('employee_id', $employeeIds)->whereDate('due_date', $date)->where('type', HrReward::TYPE_OVERTIME)->forceDelete();
        }

        HrAttendancePolicyLog::whereIn('employee_id', $employeeIds)
            ->whereDate('date', $date)
            ->where('hr_attendance_policy_id', $policy->id)
            ->delete();
    }

    private function prepareAbsenceData(Collection $employeeIds, Carbon $date): array
    {
        $startOfYear = $date->copy()->startOfYear();

        $absencesCount = HrTimeTrack::whereIn('employee_id', $employeeIds)
            ->where('type', HrTimeTrack::TYPE_ABSENT)
            ->whereBetween('date', [$startOfYear, $date])
            ->whereDoesntHave('timeTrackDetails', function ($q) {
                $q->where('type', HrTimeTrackDetails::TYPE_JUSTIFICATION);
            })
            ->select('employee_id', DB::raw('count(*) as count'))
            ->groupBy('employee_id')
            ->get()
            ->pluck('count', 'employee_id');

        return [
            'absencesCount' => $absencesCount,
        ];
    }

    private function prepareLateData(Collection $employeeIds, Carbon $date): array
    {
        $startOfMonth = $date->copy()->startOfMonth();

        $allDelaysThisMonth = HrTimeTrackDetails::query()
            ->join('hr_time_tracks', 'hr_time_track_details.hr_time_track_id', '=', 'hr_time_tracks.id')
            ->whereIn('hr_time_tracks.employee_id', $employeeIds)
            ->whereBetween('hr_time_tracks.date', [$startOfMonth->toDateString(), $date->toDateString()])
            ->where('hr_time_track_details.type', '!=', HrTimeTrackDetails::TYPE_JUSTIFICATION)
            ->where(function ($q) {
                $q->where('hr_time_track_details.delay', '>', 0)->orWhere('hr_time_track_details.early_leave', '>', 0);
            })
            ->select('hr_time_tracks.employee_id', 'hr_time_track_details.id', 'hr_time_track_details.delay', 'hr_time_track_details.early_leave', 'hr_time_tracks.date', 'hr_time_track_details.shift_from', 'hr_time_track_details.shift_to')
            ->orderBy('hr_time_tracks.date')
            ->orderBy('hr_time_track_details.id')
            ->get()
            ->groupBy('employee_id');

        return [
            'allDelaysThisMonth' => $allDelaysThisMonth,
        ];
    }

    private function processAbsencePolicy(HrEmployee $employee, HrAttendancePolicy $policy, HrTimeTrack $timeTrack, Carbon $date, array $policyData): bool
    {
        if ($timeTrack->type !== HrTimeTrack::TYPE_ABSENT) {
            return false;
        }

        $isJustified = $timeTrack->timeTrackDetails->contains('type', HrTimeTrackDetails::TYPE_JUSTIFICATION);
        if ($isJustified) {
            return false;
        }

        $absencesCount = $policyData['absencesCount']->get($employee->id, 0);

        $occurrenceMap = [1 => 'first', 2 => 'second', 3 => 'third', 4 => 'fourth'];
        $occurrenceKey = $occurrenceMap[$absencesCount] ?? 'fourth';
        $daysToDeduct = $policy->settings['absence'][$occurrenceKey] ?? null;

        if (!$daysToDeduct) {
            Log::warning("No absence penalty setting for occurrence {$absencesCount} for employee {$employee->id}");
            return false;
        }

        $amount = $this->calculateAbsencePenalty($daysToDeduct, $policy, $employee);
        if ($amount <= 0) {
            return false;
        }

        HrPenalty::create([
            'employee_id' => $employee->id,
            'description' => "خصم غياب لتاريخ {$date->toDateString()} - المرة رقم {$absencesCount} ({$daysToDeduct} أيام)",
            'amount' => $amount,
            'due_date' => $date,
            'status' => HrPenalty::STATUS_APPROVED,
            'type' => HrPenalty::TYPE_ABSENCE,
        ]);

        HrAttendancePolicyLog::create([
            'hr_time_track_id' => $timeTrack->id,
            'employee_id' => $employee->id,
            'hr_attendance_policy_id' => $policy->id,
            'date' => $date->toDateString(),
            'policy_type' => $policy->type,
            'calculated_amount' => $amount,
            'details' => [
                'type' => 'absence',
                'absences_count' => $absencesCount,
                'days_deducted' => $daysToDeduct,
            ],
            'applied_at' => now(),
        ]);

        Log::info("Applied absence penalty for {$employee->name}: {$amount} SAR");
        return true;
    }

    private function processLatePolicy(HrEmployee $employee, HrAttendancePolicy $policy, HrTimeTrack $timeTrack, Carbon $date, array $policyData): bool
    {
        $processed = false;
        $employeeDelaysThisMonth = $policyData['allDelaysThisMonth']->get($employee->id, collect());

        foreach ($timeTrack->timeTrackDetails as $detail) {
            if ($detail->type === HrTimeTrackDetails::TYPE_JUSTIFICATION) {
                continue;
            }

            foreach (['delay', 'early_leave'] as $type) {
                $delayInSeconds = $detail->$type ?? 0;
                if ($delayInSeconds <= 0) {
                    continue;
                }

                //   dd(  $delayInSeconds );

                $delayInMinutes = secondsToMinutes($delayInSeconds);
                $latenessRangeKey = $this->getLatenessRangeKey($delayInMinutes, $type);
                if (!$latenessRangeKey) {
                    continue;
                }

                $lateCount = $employeeDelaysThisMonth
                    ->filter(function ($historicalDelay) use ($date, $detail, $type, $latenessRangeKey) {
                        $isSameDayAndEarlier = $historicalDelay->date == $date->toDateString() && $historicalDelay->id <= $detail->id;
                        $isPreviousDay = $historicalDelay->date < $date->toDateString();

                        if (!($isSameDayAndEarlier || $isPreviousDay)) {
                            return false;
                        }

                        $historicalDelayMinutes = secondsToMinutes($historicalDelay->$type ?? 0);
                        return $this->getLatenessRangeKey($historicalDelayMinutes, $type) === $latenessRangeKey;
                    })
                    ->count();

                if ($lateCount == 0) {
                    continue;
                }

                $occurrenceMap = [1 => 'first', 2 => 'second', 3 => 'third', 4 => 'fourth'];
                $occurrenceKey = $occurrenceMap[$lateCount] ?? 'fourth';

                $penaltyValue = $policy->settings['delay'][$latenessRangeKey]['daily'][$occurrenceKey] ?? null;
                if (!$penaltyValue) {
                    continue;
                }

                $amount = $this->calculateDelayPenalty($penaltyValue, $delayInMinutes, $policy, $employee);
                if ($amount <= 0) {
                    continue;
                }

                $penaltyType = $type === 'delay' ? 'تأخير' : 'انصراف مبكر';
                HrPenalty::create([
                    'employee_id' => $employee->id,
                    'description' => "خصم {$penaltyType} لتاريخ {$date->toDateString()} - وردية {$detail->shift_from} - {$detail->shift_to} ({$delayInMinutes} دقيقة - المرة رقم {$lateCount})",
                    'amount' => $amount,
                    'due_date' => $date,
                    'status' => HrPenalty::STATUS_APPROVED,
                    'type' => HrPenalty::TYPE_LATE,
                ]);

                HrAttendancePolicyLog::create([
                    'hr_time_track_id' => $timeTrack->id,
                    'employee_id' => $employee->id,
                    'hr_attendance_policy_id' => $policy->id,
                    'date' => $date->toDateString(),
                    'policy_type' => $policy->type,
                    'calculated_amount' => $amount,
                    'details' => [
                        'type' => $type,
                        'delay_minutes' => $delayInMinutes,
                        'occurrence' => $lateCount,
                    ],
                    'applied_at' => now(),
                ]);

                Log::info("Applied {$penaltyType} penalty for {$employee->name}: {$amount} SAR");
                $processed = true;
            }
        }
        return $processed;
    }

    private function processOvertimePolicy(HrEmployee $employee, HrAttendancePolicy $policy, HrTimeTrack $timeTrack, Carbon $date): bool
    {
        if ($timeTrack->timeTrackDetails->isEmpty()) {
            return false;
        }

        $processed = false;

        foreach ($timeTrack->timeTrackDetails as $details) {
            if (!isset($details->overtime) || $details->overtime <= 0) {
                continue;
            }

            $overtimeRate = $policy->settings['overtime_rate'] ?? null;
            if (!$overtimeRate) {
                continue;
            }

            $hourAmount = $employee->salary->hour_amount ?? $employee->salary->basic / 176;
            if ($hourAmount <= 0) {
                continue;
            }

            $overtimeInHours = round($details->overtime / 3600, 2);
            $amount = $overtimeInHours * $hourAmount * ($overtimeRate / 100);

            HrReward::create([
                'employee_id' => $employee->id,
                'type' => HrReward::TYPE_OVERTIME,
                'amount' => round($amount, 2),
                'status' => HrReward::STATUS_APPROVED,
                'over_time' => $details->overtime,
                'due_date' => $date,
                'note' => "أجر وقت إضافي لتاريخ {$date->toDateString()} - وردية {$details->shift_from} - {$details->shift_to} ({$overtimeInHours} ساعة)",
            ]);

            HrAttendancePolicyLog::create([
                'hr_time_track_id' => $timeTrack->id,
                'employee_id' => $employee->id,
                'hr_attendance_policy_id' => $policy->id,
                'date' => $date->toDateString(),
                'policy_type' => $policy->type,
                'calculated_amount' => round($amount, 2),
                'details' => [
                    'type' => 'overtime',
                    'overtime_seconds' => $details->overtime,
                    'overtime_hours' => $overtimeInHours,
                ],
                'applied_at' => now(),
            ]);
            $processed = true;
        }
        return $processed;
    }

    private function calculateAbsencePenalty($daysToDeduct, HrAttendancePolicy $policy, HrEmployee $employee): float
    {
        $salary = $employee->salary;
        if (!$salary) {
            return 0;
        }

        $baseDayAmount = 0;
        if ($policy->salary_effect_basic) {
            $baseDayAmount += $salary->basic / 30;
        }
        $baseDayAmount += $this->calculateAllowancesAmount($policy, $employee, 30);

        return round((float) $daysToDeduct * $baseDayAmount, 2);
    }

    private function calculateDelayPenalty($penaltyValue, int $delayInMinutes, HrAttendancePolicy $policy, HrEmployee $employee): float
    {
        $salary = $employee->salary;
        if (!$salary) {
            return 0;
        }

        $divisor = $policy->calculation_type === HrAttendancePolicy::CALCULATION_TYPE_DAY ? 30 : 176;
        $baseAmount = 0;
        if ($policy->salary_effect_basic) {
            $baseAmount += $salary->basic / $divisor;
        }
        $baseAmount += $this->calculateAllowancesAmount($policy, $employee, $divisor);

        if (str_contains($penaltyValue, '%')) {
            $percentage = (float) trim($penaltyValue, '%');
            $amount = ($percentage / 100) * $baseAmount;
        } else {
            $amount = (float) $penaltyValue;
        }
        return round($amount, 2);
    }

    private function calculateAllowancesAmount(HrAttendancePolicy $policy, HrEmployee $employee, int $divisor): float
    {
        $allowanceIds = $policy->salary_effect_allowances;
        if (empty($allowanceIds) || !$employee->salary) {
            return 0;
        }

        $total = $employee->salary->salary_allowances->whereIn('allowance_id', $allowanceIds)->sum('amount');

        return round($total / $divisor, 2);
    }

    private function getLatenessRangeKey(int $delayInMinutes, string $type): ?string
    {
        if ($type === 'early_leave') {
            if ($delayInMinutes <= 15) {
                return 'early_15';
            }
            return 'early_15_plus';
        }
        if ($delayInMinutes <= 15) {
            return '0_15';
        }
        if ($delayInMinutes <= 30) {
            return '15_30';
        }
        if ($delayInMinutes <= 60) {
            return '30_60';
        }
        return '60_plus';
    }

    public function resolvePolicyForEmployee(HrEmployee $employee, int $type, Carbon $date): ?HrAttendancePolicy
    {
        $activePolicies = HrAttendancePolicy::where('status', HrAttendancePolicy::STATUS_ACTIVE)
            ->where('type', $type)
            ->where(function ($q) use ($date) {
                $q->whereNull('start_date')->orWhere('start_date', '<=', $date->toDateString());
            })
            ->where(function ($q) use ($date) {
                $q->whereNull('end_date')->orWhere('end_date', '>=', $date->toDateString());
            })
            ->get();

        if ($activePolicies->isEmpty()) {
            return null;
        }

        // 1. Employee Scope (SCOPE_EMPLOYEE = 1)
        $employeePolicy = $activePolicies->first(function ($p) use ($employee) {
            return $p->scope == HrAttendancePolicy::SCOPE_EMPLOYEE && $p->isInScope($employee->id);
        });
        if ($employeePolicy) return $employeePolicy;

        // 2. Job Scope (SCOPE_JOB = 3)
        $jobId = $employee->job_id ?? $employee->hr_job_id ?? null;
        if ($jobId) {
            $jobPolicy = $activePolicies->first(function ($p) use ($jobId) {
                return $p->scope == HrAttendancePolicy::SCOPE_JOB && $p->isInScope($jobId);
            });
            if ($jobPolicy) return $jobPolicy;
        }

        // 3. Department Scope (SCOPE_DEPARTMENT = 2)
        $deptId = $employee->department_id ?? $employee->hr_department_id ?? null;
        if ($deptId) {
            $deptPolicy = $activePolicies->first(function ($p) use ($deptId) {
                return $p->scope == HrAttendancePolicy::SCOPE_DEPARTMENT && $p->isInScope($deptId);
            });
            if ($deptPolicy) return $deptPolicy;
        }

        // 4. Branch Scope (SCOPE_BRANCH = 4)
        if (isset($employee->branch_id)) {
            $branchPolicy = $activePolicies->first(function ($p) use ($employee) {
                return $p->scope == HrAttendancePolicy::SCOPE_BRANCH && $p->isInScope($employee->branch_id);
            });
            if ($branchPolicy) return $branchPolicy;
        }

        // 5. Global Policy
        return $activePolicies->first(function ($p) {
            return empty($p->scope_ids_list);
        });
    }
}
