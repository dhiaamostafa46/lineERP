<?php

namespace Modules\HR\App\Models;


use Illuminate\Database\Eloquent\Model;

class HrShiftTypeTranslation extends Model
{
    protected $table = 'hr_shift_type_translations';

    public $fillable = ['name'];

    public $timestamps = false;
}
