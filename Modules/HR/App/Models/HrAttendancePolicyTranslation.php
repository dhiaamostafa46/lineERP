<?php

namespace Modules\HR\App\Models;

use Illuminate\Database\Eloquent\Model;

class HrAttendancePolicyTranslation extends Model
{
    public $table = 'hr_attendance_policy_translations';

    public $fillable = ['name'];

    public $timestamps = false;
}
