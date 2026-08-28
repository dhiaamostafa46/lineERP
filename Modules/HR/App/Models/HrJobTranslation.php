<?php

namespace Modules\HR\App\Models;


use Illuminate\Database\Eloquent\Model;

class HrJobTranslation extends Model
{
    protected $table = 'hr_job_translations';

    public $fillable = ['name'];

    public $timestamps = false;
}
