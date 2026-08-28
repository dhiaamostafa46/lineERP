<?php

namespace Modules\HR\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HrTerminationContract extends Model
{
    use SoftDeletes;
    public $table = 'hr_termination_contracts';

    public $fillable = [
        'termination_id',
        'contract_id',
        'worked_days'
    ];

    protected $casts = [
        'id'             => 'integer',
        'termination_id' => 'integer',
        'contract_id'    => 'integer',
        'worked_days'    => 'integer'
    ];

    public static array $rules = [
        'termination_id' => 'required|exists:hr_terminations,id',
        'contract_id'    => 'required|exists:hr_contracts,id',
        'worked_days'    => 'required|integer'
    ];
}
