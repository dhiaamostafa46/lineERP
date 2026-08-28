<?php

namespace Modules\HR\App\Models;


use Illuminate\Database\Eloquent\Model;

class HrAllowanceTranslation extends Model
{
    protected $table = 'hr_allowance_translations';

    public $fillable = ['name'];

    public $timestamps = false;
}
