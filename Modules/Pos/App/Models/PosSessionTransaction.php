<?php

namespace Modules\Pos\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Pos\Database\Factories\PosSessionTransactionFactory;

class PosSessionTransaction extends Model
{
    use HasFactory;

    const TYPE_SALE = 'sale';
    const TYPE_RETURN = 'return';
    const TYPE_WITHDRAWAL = 'withdrawal';
    const TYPE_DEPOSIT = 'deposit';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'uuid',
        'pos_session_id',
        'pos_payment_method_id',
        'user_id',
        'amount',
        'type',
        'notes',
        'reference_id'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    /**
     * علاقات
     */
    public function session()
    {
        return $this->belongsTo(PosSession::class, 'pos_session_id');
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PosPaymentMethod::class, 'pos_payment_method_id');
    }

    /**
     * Accessors & Mutators
     */

    /**
     * Static Methods
     */

    /**
     * Scopes
     */

    protected static function newFactory(): PosSessionTransactionFactory
    {
        //return PosSessionTransactionFactory::new();
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) \Illuminate\Support\Str::uuid();
            }
        });
    }
}
