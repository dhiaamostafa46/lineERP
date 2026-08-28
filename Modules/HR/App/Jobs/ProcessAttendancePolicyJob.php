<?php

namespace Modules\HR\App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\HR\App\Models\HrEmployee;
use Modules\HR\App\Models\HrAttendancePolicy;
use Carbon\Carbon;
use Modules\HR\App\Services\AttendancePolicyService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessAttendancePolicyJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $employee;
    protected $policy;
    protected $date;

    /**
     * Create a new job instance.
     */
    public function __construct(HrEmployee $employee, HrAttendancePolicy $policy, Carbon $date)
    {
        $this->employee = $employee;
        $this->policy = $policy;
        $this->date = $date;
    }

    /**
     * Execute the job.
     */
    public function handle(AttendancePolicyService $service)
    {
        try {
            DB::transaction(function () use ($service) {
                $service->processEmployeePolicy($this->employee, $this->policy, $this->date);
            });
        } catch (\Exception $e) {
            Log::error("Job Error processing {$this->employee->name}: " . $e->getMessage());
            throw $e;
        }
    }
}
