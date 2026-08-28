<?php

namespace Modules\AccuSoft\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AssetImpairment extends Model
{
    use SoftDeletes;

    protected $table = 'asset_impairments';

    protected $guarded = ['id'];

    protected $casts = [
        'assessment_date' => 'date',
        'is_reversed' => 'boolean',
    ];

    public function asset()
    {
        return $this->belongsTo(Asset::class, 'asset_id');
    }

    public function journalEntry()
    {
        return $this->belongsTo(AsJournalEntry::class, 'journal_entry_id');
    }
}
