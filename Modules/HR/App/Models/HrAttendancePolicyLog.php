<?php

namespace Modules\HR\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HrAttendancePolicyLog extends Model
{
    use HasFactory;

    public $table = 'hr_attendance_policy_logs';

    protected $fillable = [
        'hr_time_track_id',
        'employee_id',
        'hr_attendance_policy_id',
        'date',
        'policy_type',
        'calculated_amount',
        'details',
        'applied_at',
    ];

    protected $casts = [
        'date' => 'date',
        'calculated_amount' => 'float',
        'details' => 'array',
        'applied_at' => 'datetime',
    ];

    public function employee()
    {
        return $this->belongsTo(HrEmployee::class, 'employee_id');
    }

    public function policy()
    {
        return $this->belongsTo(HrAttendancePolicy::class, 'hr_attendance_policy_id');
    }

    public function timeTrack()
    {
        return $this->belongsTo(HrTimeTrack::class, 'hr_time_track_id');
    }
}
