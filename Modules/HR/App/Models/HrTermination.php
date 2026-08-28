<?php

namespace Modules\HR\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HrTermination extends Model
{
    use SoftDeletes;
    public $table = 'hr_terminations';

    public $fillable = [
        'termination_type_id',
        'employee_id',
        'worked_days',
        'last_reward'
    ];

    protected $casts = [
        'id'                  => 'integer',
        'termination_type_id' => 'integer',
        'employee_id'         => 'integer',
        'worked_days'         => 'integer',
        'last_reward'         => 'decimal:2'
    ];

    public static array $rules = [
        'termination_type_id' => 'required|exists:hr_termination_types,id',
        'employee_id'         => 'required|exists:hr_employees,id',
        'worked_days'         => 'required|integer',
        'last_reward'         => 'required|numeric'
    ];
}
