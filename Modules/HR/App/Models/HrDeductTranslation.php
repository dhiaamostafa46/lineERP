<?php

namespace Modules\HR\App\Models;


use Illuminate\Database\Eloquent\Model;

class HrDeductTranslation extends Model
{
    protected $table = 'hr_deduct_translations';

    public $fillable = ['name'];

    public $timestamps = false;
}
