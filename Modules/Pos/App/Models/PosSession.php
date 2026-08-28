<?php

namespace Modules\Pos\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Pos\Database\Factories\PosSessionFactory;

class PosSession extends Model
{
    use HasFactory;

    const STATUS_OPEN = 'open';
    const STATUS_CLOSED = 'closed';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'uuid',
        'device_id',
        'user_id',
        'opening_balance',
        'opened_at',
        'closed_at',
        'expected_cash',
        'actual_cash',
        'difference',
        'status',
        'closing_journal_entry_id',
        'browser_session_token'
    ];

    protected $casts = [
        'opened_at' => 'datetime',
        'closed_at' => 'datetime',
        'opening_balance' => 'decimal:2',
        'expected_cash' => 'decimal:2',
        'actual_cash' => 'decimal:2',
        'difference' => 'decimal:2',
    ];

    /**
     * علاقات
     */
    public function device()
    {
        return $this->belongsTo(PosDevice::class, 'device_id');
    }

    public function cashier()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    public function transactions()
    {
        return $this->hasMany(PosSessionTransaction::class, 'pos_session_id');
    }

    public function invoices()
    {
        return $this->hasMany(\App\Models\invApp\SalesInvoice::class, 'pos_session_id');
    }

    public function audits()
    {
        return $this->hasMany(PosSessionAudit::class, 'pos_session_id');
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

    protected static function newFactory(): PosSessionFactory
    {
        //return PosSessionFactory::new();
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
