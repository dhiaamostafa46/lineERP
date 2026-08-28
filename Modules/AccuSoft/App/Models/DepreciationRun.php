<?php

namespace Modules\AccuSoft\App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class DepreciationRun extends Model
{
    use \App\Traits\BelongsToBranch;

    protected $table = 'depreciation_runs';

    protected $fillable = [
        'branch_id',
        'run_name',
        'run_date',
        'run_month',
        'run_year',
        'total_depreciation',
        'journal_entry_id',
        'status',
        'notes',
        'created_by',
    ];

    public function branch()
    {
        return $this->belongsTo(\App\Models\Branch::class, 'branch_id');
    }

    public function journalEntry()
    {
        return $this->belongsTo(AsJournalEntry::class, 'journal_entry_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
