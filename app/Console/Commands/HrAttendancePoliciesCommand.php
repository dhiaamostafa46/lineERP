<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\HR\App\Models\HrAttendancePolicy;
use Modules\HR\App\Models\HrEmployee;
use Modules\HR\App\Services\AttendancePolicyService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

class HrAttendancePoliciesCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'attendance:policies';

    /**
     * The console command description.
     */
    protected $description = 'Process attendance policies for yesterday according to Saudi Labor Law.';

    protected $policyService;

    /**
     * Create a new command instance.
     */
    public function __construct(AttendancePolicyService $policyService)
    {
        parent::__construct();
        $this->policyService = $policyService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $date = Carbon::yesterday()->startOfDay();
        $policies = HrAttendancePolicy::where('status', HrAttendancePolicy::STATUS_ACTIVE)->where('is_automatic', true)->get();
        if ($policies->isEmpty()) {
            return;
        }

        foreach ($policies as $policy) {
            $employees = $this->getPolicyEmployees($policy);
            if ($employees->isEmpty()) {
                continue;
            }
            try {
                $result = $this->policyService->processPolicyForEmployees($employees, $policy, $date);
            } catch (\Exception $e) {
            }
        }
    }

    /**
     * Get employees based on policy scope.
     */
    private function getPolicyEmployees(HrAttendancePolicy $policy): Collection
    {
        $query = HrEmployee::query();

        switch ($policy->scope) {
            case HrAttendancePolicy::SCOPE_EMPLOYEE:
                $query->whereIn('id', $policy->scope_ids_list ?? []);
                break;
            case HrAttendancePolicy::SCOPE_DEPARTMENT:
                $query->whereIn('department_id', $policy->scope_ids_list ?? []);
                break;
            case HrAttendancePolicy::SCOPE_JOB:
                $query->whereIn('job_id', $policy->scope_ids_list ?? []);
                break;
            case HrAttendancePolicy::SCOPE_BRANCH:
                $query->whereIn('branch_id', $policy->scope_ids_list ?? []);
                break;
        }

        return $query->with(['salary.salary_allowances', 'shift.shifts'])->get();
    }

    /**
     * Get the console command arguments.
     */
    protected function getArguments(): array
    {
        return [];
    }

    /**
     * Get the console command options.
     */
    protected function getOptions(): array
    {
        return [];
    }
}
