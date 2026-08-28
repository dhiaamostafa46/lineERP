<?php

namespace Modules\HR\App\Models;


use Illuminate\Database\Eloquent\Model;

class HrHolidayTypeTranslation extends Model
{
    protected $table = 'hr_holiday_type_translations';

    public $fillable = ['name'];

    public $timestamps = false;
}
