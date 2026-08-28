<?php

namespace Modules\HR\App\Models;


use Illuminate\Database\Eloquent\Model;

class HrGroupTranslation extends Model
{
    protected $table = 'hr_group_translations';

    public $fillable = ['name'];

    public $timestamps = false;
}
