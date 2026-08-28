<?php

namespace Modules\HR\App\Models;


use Illuminate\Database\Eloquent\Model;

class HrDepartmentTranslation extends Model
{
    protected $table = 'hr_department_translations';

    public $fillable = ['name'];

    public $timestamps = false;
}
