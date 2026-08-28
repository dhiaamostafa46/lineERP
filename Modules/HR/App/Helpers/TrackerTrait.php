<?php

namespace Modules\HR\App\Helpers;

use Modules\HR\App\Models\HrEmployee;
use Modules\HR\App\Models\HrTracker;
use Modules\HR\App\Models\HrTrackerJob;
use Modules\HR\App\Models\HrTrackingApproval;

trait TrackerTrait
{

    public function getTracker() {}

    public function setTracker($model, $hr_employee_id, $type)
    {


        $hr_employee = HrEmployee::find($hr_employee_id);
        if ($hr_employee) {
            $job = $hr_employee->job_id;
            $tracker = HrTracker::where('type', $type)
                ->where('status', HrTracker::STATUS_ACTIVE)
                ->where('department_id', $hr_employee->department_id)
                ->whereIn('id', function ($query) use ($job) {
                    $query->select('tracker_id')
                        ->from(with(new HrTrackerJob)->getTable())
                        ->where('job_id', $job);
                })
                ->first();

               

            if ($tracker) {
                $approval_ids = [];
                foreach ($tracker->tracker_approvals as $approval) {
                    $approval_ids[] = $approval['user_id'];
                    HrTrackingApproval::updateOrCreate([
                        'trackable_id'   => $model->id,
                        'trackable_type' => get_class($model),
                        'user_id'        => $approval['user_id'],
                    ], [
                        'status'         => HrTrackingApproval::STATUS_PENDING,
                        'sort'           => $approval['sort'],
                        'is_current'     => $approval['sort'] == 1 ? 1 : 0,
                    ]);
                }

                HrTrackingApproval::where('trackable_id', $model->id)
                    ->where('trackable_type', get_class($model))
                    ->whereNotIn('user_id', $approval_ids)
                    ->delete();
            }
        }
    }
}
