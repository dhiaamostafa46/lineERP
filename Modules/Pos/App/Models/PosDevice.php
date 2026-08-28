<?php

namespace Modules\Pos\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;
use Modules\Pos\Database\Factories\PosDeviceFactory;

class PosDevice extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'uuid', 'name', 'branch_id', 'store_id', 'is_active', 'is_users_linked', 'linked_users',
        'default_customer_id', 'shortage_account_id', 'overage_account_id', 
        'main_safe_account_id', 'expense_account_id', 'sales_account_id', 'discount_account_id', 
        'vat_account_id', 'cogs_account_id', 'inventory_account_id',
        'auto_journal_entry', 'enable_cash_movements', 'allow_negative_stock', 'auto_print_receipt',
        'allow_price_modification', 'allow_discount_modification', 'show_available_qty', 'enable_pos_returns',
        'print_copies_count', 'prices_include_tax', 'send_to_zatca_phase2'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_users_linked' => 'boolean',
        'linked_users' => 'array',
        'auto_journal_entry' => 'boolean',
        'enable_cash_movements' => 'boolean',
        'allow_negative_stock' => 'boolean',
        'auto_print_receipt' => 'boolean',
        'allow_price_modification' => 'boolean',
        'allow_discount_modification' => 'boolean',
        'show_available_qty' => 'boolean',
        'enable_pos_returns' => 'boolean',
        'print_copies_count' => 'integer',
        'prices_include_tax' => 'boolean',
        'send_to_zatca_phase2' => 'boolean',
    ];

    /**
     * Boot function for using with User Events
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'uuid';
    }

    /**
     * علاقات
     */
    public function branch()
    {
        return $this->belongsTo(\App\Models\Branch::class);
    }

    public function store()
    {
        return $this->belongsTo(\App\Models\StoreApp\Store::class);
    }

    public function defaultCustomer()
    {
        return $this->belongsTo(\App\Models\User::class, 'default_customer_id');
    }

    public function paymentMethods()
    {
        return $this->hasMany(PosPaymentMethod::class, 'device_id');
    }

    /**
     * Accessors & Mutators
     */
    public function getIsActiveTextAttribute()
    {
        return $this->is_active ? __('pos::models/devices.fields.is_active') : '---';
    }

    public function getIsActiveBadgeAttribute()
    {
        return $this->is_active ? 'badge-success' : 'badge-secondary';
    }

    public function getAuthModeAttribute()
    {
        return $this->is_users_linked ? 'pin' : 'system_user';
    }

    /**
     * Static Methods
     */

    /**
     * Scopes
     */

    protected static function newFactory(): PosDeviceFactory
    {
        //return PosDeviceFactory::new();
    }
}
