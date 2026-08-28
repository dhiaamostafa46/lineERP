<?php

namespace Modules\HR\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HrTerminationTypeReward extends Model
{
    use SoftDeletes;
    public $table = 'hr_termination_type_rewards';

    public $fillable = [
        'termination_type_id',
        'percentage',
        'worked_days',
        'fixed_amount'
    ];

    protected $casts = [
        'id'                  => 'integer',
        'termination_type_id' => 'integer',
        'percentage'          => 'integer',
        'worked_days'         => 'integer',
        'fixed_amount'        => 'integer'
    ];

    public static array $rules = [
        'termination_type_id' => 'required|exists:hr_termination_types,id',
        'percentage'          => 'required|integer',
        'worked_days'         => 'required|integer',
        'fixed_amount'        => 'required|integer'
    ];
}
