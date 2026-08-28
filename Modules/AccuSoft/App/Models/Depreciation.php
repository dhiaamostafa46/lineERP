<?php

namespace Modules\AccuSoft\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Depreciation extends Model
{
    use SoftDeletes;

    protected $table = 'depreciations';
    protected $guarded = ['id'];

    protected $casts = [
        'period_date' => 'date',
        'is_posted' => 'boolean',
        'is_locked' => 'boolean',
        'is_reversed' => 'boolean',
    ];

    // Constants for entry type
    public const ENTRY_MONTHLY = 1;
    public const ENTRY_DISPOSAL = 2;
    public const ENTRY_ADJUSTMENT = 3;
    public const ENTRY_REVERSAL = 4;

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    public function costCenter()
    {
        return $this->belongsTo(\App\Models\AccuSoft\CostCenters::class, 'cost_center_id');
    }

    public function depreciationRun()
    {
        return $this->belongsTo(DepreciationRun::class, 'depreciation_run_id');
    }
}
