<?php

namespace Modules\AccuSoft\App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetTransaction extends Model
{
    use \App\Traits\BelongsToBranch;

    protected $table = 'asset_transactions';

    // Transaction Types
    public const TYPE_PURCHASE = 1;
    public const TYPE_DEPRECIATION = 2;
    public const TYPE_DISPOSAL = 3;
    public const TYPE_MAINTENANCE = 4;
    public const TYPE_REVALUATION = 5;

    public static function getTransactionTypes()
    {
        return [
            self::TYPE_PURCHASE => __('lang.purchase', [], 'ar') ?? 'شراء',
            self::TYPE_DEPRECIATION => __('lang.depreciation', [], 'ar') ?? 'إهلاك',
            self::TYPE_DISPOSAL => __('lang.disposal', [], 'ar') ?? 'استبعاد',
            self::TYPE_MAINTENANCE => __('lang.maintenance', [], 'ar') ?? 'صيانة',
            self::TYPE_REVALUATION => __('lang.revaluation', [], 'ar') ?? 'إعادة تقييم',
        ];
    }

    protected $fillable = [
        'branch_id',
        'asset_id',
        'transaction_type',
        'transaction_date',
        'amount',
        'notes',
        'journal_entry_id',
    ];

    protected $casts = [
        'transaction_date' => 'date',
    ];

    public function asset()
    {
        return $this->belongsTo(Asset::class, 'asset_id');
    }

    public function branch()
    {
        return $this->belongsTo(\App\Models\Branch::class, 'branch_id');
    }

    public function journalEntry()
    {
        return $this->belongsTo(AsJournalEntry::class, 'journal_entry_id');
    }
}
