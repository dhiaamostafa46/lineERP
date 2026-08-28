<?php

namespace Modules\HR\App\Models;

use Illuminate\Database\Eloquent\Model;

class HrCalendarEventTranslation extends Model
{
    protected $foreignKey = 'hr_calendar_event_id';
    public $table = 'hr_calendar_event_translations';
    public $timestamps = false;

    protected $fillable = ['name'];
}
