<?php

namespace Modules\HR\App\Models;


use Illuminate\Database\Eloquent\Model;

class HrReportTypeTranslation extends Model
{
    protected $table = 'hr_report_type_translations';

    public $fillable = ['name', 'description'];

    public $timestamps = false;
}
