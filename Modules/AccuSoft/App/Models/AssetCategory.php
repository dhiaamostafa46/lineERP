<?php

namespace Modules\AccuSoft\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\AccuSoft\TreeAccounts;

use Astrotomic\Translatable\Translatable;

class AssetCategory extends Model
{
    use \App\Traits\BelongsToBranch;

    use SoftDeletes, Translatable;

    public const METHOD_NONE = 'none';
    public const METHOD_STRAIGHT_LINE = 'straight_line';
    public const METHOD_DECLINING_BALANCE = 'declining_balance';

    public const STATUS_INACTIVE = 0;
    public const STATUS_ACTIVE = 1;

    public $translationForeignKey = 'asset_id';

    public static function getDepreciationMethods()
    {
        return [
            self::METHOD_NONE => __('accusoft::models/as_asset_categories.methods.none') ?? 'لا يوجد',
            self::METHOD_STRAIGHT_LINE => __('accusoft::models/as_asset_categories.methods.straight_line'),
            self::METHOD_DECLINING_BALANCE => __('accusoft::models/as_asset_categories.methods.declining_balance'),
        ];
    }

    public $translatedAttributes = ['name', 'description', 'notes'];

    protected $table = 'asset_categories';

    protected $fillable = [
        'branch_id',
        'has_accounting_effect',
        'name',
        'asset_account_id',
        'accumulated_depreciation_account_id',
        'depreciation_expense_account_id',
        'default_depreciation_method',
        'default_useful_life',
        'calculation_type',
        'useful_life_type',
        'status',
    ];

    public function assetAccount()
    {
        return $this->belongsTo(TreeAccounts::class, 'asset_account_id');
    }

    public function accumulatedDepreciationAccount()
    {
        return $this->belongsTo(TreeAccounts::class, 'accumulated_depreciation_account_id');
    }

    public function depreciationExpenseAccount()
    {
        return $this->belongsTo(TreeAccounts::class, 'depreciation_expense_account_id');
    }

    public function getHasAccountingEffectTextAttribute()
    {
        return $this->has_accounting_effect ? __('lang.yes') : __('lang.no');
    }

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }
}
