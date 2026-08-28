<?php

namespace Modules\Pos\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Pos\Database\Factories\PosPaymentMethodFactory;

class PosPaymentMethod extends Model
{
    use HasFactory;

    const TYPE_CASH = 'cash';
    const TYPE_CARD = 'card';
    const TYPE_TRANSFER = 'transfer';
    const TYPE_CREDIT = 'credit';
    const TYPE_INSTALLMENT = 'installment';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['device_id', 'name', 'type', 'account_id', 'is_default', 'is_active'];

    /**
     * علاقات
     */
    public function device()
    {
        return $this->belongsTo(PosDevice::class, 'device_id');
    }

    public function account()
    {
        return $this->belongsTo(\App\Models\AccuSoft\TreeAccounts::class, 'account_id');
    }

    /**
     * Accessors & Mutators
     */
    public function getTypeTextAttribute()
    {
        return __('pos::models/payment_methods.types.' . $this->type);
    }

    public function getIsActiveTextAttribute()
    {
        return $this->is_active ? __('pos::models/payment_methods.fields.is_active') : '---';
    }

    public function getIsActiveBadgeAttribute()
    {
        return $this->is_active ? 'badge-success' : 'badge-secondary';
    }

    /**
     * Static Methods
     */
    public static function types()
    {
        return [
            self::TYPE_CASH => __('pos::models/payment_methods.types.cash'),
            self::TYPE_CARD => __('pos::models/payment_methods.types.card'),
            self::TYPE_TRANSFER => __('pos::models/payment_methods.types.transfer'),
            self::TYPE_CREDIT => __('pos::models/payment_methods.types.credit'),
            self::TYPE_INSTALLMENT => __('pos::models/payment_methods.types.installment'),
        ];
    }

    /**
     * Scopes
     */

    protected static function newFactory(): PosPaymentMethodFactory
    {
        //return PosPaymentMethodFactory::new();
    }
}
