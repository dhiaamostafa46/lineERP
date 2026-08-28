<?php

namespace Modules\HR\App\Services;

use Carbon\Carbon;
use Modules\HR\App\Models\HrEndService;
use Modules\HR\App\Models\HrPenalty;
use Modules\HR\App\Models\HrAdvance;
use Modules\HR\App\Models\HrDeduct;

class EosbCalculatorService
{
    /**
     * Calculate End of Service Benefit
     *
     * @param int $employeeId
     * @param Carbon|string $startDate
     * @param Carbon|string $endDate
     * @param float $salary
     * @param int|null $reason
     * @return array
     */
    public static function calculate($employeeId, $startDate, $endDate, float $salary, ?int $reason): array
    {
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        if ($start->greaterThan($end)) {
            return [
                'duration_text' => 'تاريخ النهاية يجب أن يكون بعد تاريخ البداية',
                'duration_years' => 0,
                'reward_amount' => 0,
                'total_penalties' => 0,
                'total_advances' => 0,
                'total_deducts' => 0,
                'net_reward' => 0,
            ];
        }

        // Calculate exact differences
        $diff = $start->diff($end);
        $years = $diff->y;
        $months = $diff->m;
        $days = $diff->d;

        $durationText = "{$years} سنوات، {$months} شهور، {$days} أيام";

        // Calculate total duration in years according to standard Labor Law fractions
        $totalYears = $years + ($months / 12) + ($days / 365);

        // Base Reward (Full Reward)
        $fullReward = 0;
        if ($totalYears <= 5) {
            $fullReward = ($salary / 2) * $totalYears;
        } else {
            $first5 = ($salary / 2) * 5;
            $remaining = ($totalYears - 5) * $salary;
            $fullReward = $first5 + $remaining;
        }

        $finalReward = 0;

        // Determine multiplier based on reason
        switch ((int) $reason) {
            // Reasons that grant ZERO reward
            case HrEndService::REASON_ARTICLE_80:
            case HrEndService::REASON_TRIAL_PERIOD:
                $finalReward = 0;
                break;

            // Reasons that grant FRACTIONAL reward (Resignation)
            case HrEndService::REASON_RESIGNATION:
                if ($totalYears < 2) {
                    $finalReward = 0;
                } elseif ($totalYears >= 2 && $totalYears <= 5) {
                    $finalReward = $fullReward / 3;
                } elseif ($totalYears > 5 && $totalYears < 10) {
                    $finalReward = ($fullReward * 2) / 3;
                } else {
                    // 10 years or more gives full reward in resignation
                    $finalReward = $fullReward;
                }
                break;

            // All other reasons grant FULL reward (e.g. End of Contract, Art 81, Employer Termination)
            default:
                $finalReward = $fullReward;
                break;
        }

        $rewardAmount = round(max(0, $finalReward), 2);

        // Calculate Unpaid Liabilities
        $totalPenalties = 0;
        if (class_exists(HrPenalty::class)) {
            $penaltiesInPayroll = [];
            if (class_exists(\Modules\HR\App\Models\HrPayrollTransaction::class)) {
                $penaltiesInPayroll = \Modules\HR\App\Models\HrPayrollTransaction::where('type', \Modules\HR\App\Models\HrPayrollTransaction::TYPE_PENALTY)
                    ->pluck('forable_id')
                    ->toArray();
            }

            $totalPenalties = HrPenalty::where('employee_id', $employeeId)
                ->where('status', HrPenalty::STATUS_APPROVED)
                ->whereNotIn('id', $penaltiesInPayroll)
                ->sum('amount');
        }

        $totalAdvances = 0;
        if (class_exists(HrAdvance::class)) {
            $totalAdvances = HrAdvance::getEmployeeAdvanceBalance($employeeId);
        }

        $totalDeducts = 0;
        if (class_exists(\Modules\HR\App\Models\HrSalaryDeduct::class)) {
            $totalDeducts = \Modules\HR\App\Models\HrSalaryDeduct::whereHas('salary', function($q) use ($employeeId) {
                $q->where('employee_id', $employeeId);
            })
                ->whereNull('payroll_id')
                ->sum('amount');
        }

        // Net Reward
        $netReward = $rewardAmount - ($totalPenalties + $totalAdvances + $totalDeducts);
        
        // Ensure net_reward doesn't go below 0 if liabilities exceed reward
        // (In real world, the employee might owe the company, but for End Service Benefit we can show negative net to indicate they owe money)

        return [
            'duration_text' => $durationText,
            'duration_years' => round($totalYears, 4),
            'reward_amount' => $rewardAmount,
            'total_penalties' => round($totalPenalties, 2),
            'total_advances' => round($totalAdvances, 2),
            'total_deducts' => round($totalDeducts, 2),
            'net_reward' => round($netReward, 2),
        ];
    }
}
