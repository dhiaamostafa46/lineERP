<?php

namespace Modules\HR\App\Models;

use Modules\HR\App\Models\HrTracker;
use Illuminate\Database\Eloquent\Model;

class HrTrackerJob extends Model
{

    public $table = 'hr_tracker_jobs';

    public $fillable = [
        'tracker_id',
        'job_id'
    ];

    public static array $rules = [
        'tracker_id' => 'required|exists:hr_trackers,id',
        'job_id'     => 'required|exists:hr_jobs,id'
    ];

    public $timestamps = false;

    public function tracker()
    {
        return $this->belongsTo(HrTracker::class, 'tracker_id');
    }
}
