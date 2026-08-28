<?php

namespace Modules\HR\App\Models;

use Illuminate\Database\Eloquent\Model;

class HrTerminationTypeTranslation extends Model
{
    public $table = 'hr_termination_type_translations';

    public $fillable = ['name'];

    public $timestamps = false;
}
