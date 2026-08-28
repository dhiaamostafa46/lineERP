<?php

namespace Modules\HR\App\Models;


use Illuminate\Database\Eloquent\Model;

class HrContractTypeTranslation extends Model
{
    protected $table = 'hr_contract_type_translations';

    public $fillable = ['name'];

    public $timestamps = false;
}
