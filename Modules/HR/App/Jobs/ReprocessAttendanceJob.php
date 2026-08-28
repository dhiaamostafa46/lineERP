<?php

namespace Modules\HR\App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Log;
use Modules\HR\App\Models\HrEmployee;
use Modules\HR\App\Models\HrAttendancePolicy;
use Modules\HR\App\Services\AttendancePolicyService;
use Modules\HR\App\Jobs\ProcessEmployeePresenceJob;

class ReprocessAttendanceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $startDate;
    public string $endDate;
    public ?int $employeeId;

    /**
     * Create a new job instance.
     */
    public function __construct(string $startDate, string $endDate, ?int $employeeId = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->employeeId = $employeeId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info("🔹 [ReprocessJob] Starting reprocessing from {$this->startDate} to {$this->endDate}");

        $period = CarbonPeriod::create($this->startDate, $this->endDate);
        $policyService = app(AttendancePolicyService::class);

        foreach ($period as $date) {
            $dateStr = $date->toDateString();

            // 1. Re-process presence & time track
            $presenceJob = new ProcessEmployeePresenceJob($dateStr);
            $presenceJob->handle();

            // 2. Re-evaluate active policies for the day
            $query = HrEmployee::query();
            if ($this->employeeId) {
                $query->where('id', $this->employeeId);
            }
            $employees = $query->get();

            if ($employees->isNotEmpty()) {
                $activePolicies = HrAttendancePolicy::where('status', HrAttendancePolicy::STATUS_ACTIVE)->get();
                foreach ($activePolicies as $policy) {
                    $policyService->processPolicyForEmployees($employees, $policy, $date);
                }
            }
        }

        Log::info("🔹 [ReprocessJob] Finished reprocessing from {$this->startDate} to {$this->endDate}");
    }
}
