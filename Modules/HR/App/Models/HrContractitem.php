<?php

namespace Modules\HR\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\HR\Database\Factories\HrContractitemFactory;

class HrContractitem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'employee_id',
        'contract_id',
        'Desc_ar',
        'Desc_En',
    ];

    /**
     * Get the employee associated with this contract item.
     */
    public function employee()
    {
        return $this->belongsTo(HrEmployee::class);
    }

    /**
     * Get the contract associated with this contract item.
     */
    public function contract()
    {
        return $this->belongsTo(HrContract::class);
    }
}
