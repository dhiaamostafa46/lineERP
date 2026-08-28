<?php

namespace App\Models\Vehicles;

use App\Models\BaseModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Drivers\App\Models\DrDriverType;
use Modules\Drivers\App\Models\DrSettlementDetail;

class Driver extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $table = 'drivers';

    protected $fillable = [
        'user_id',
        'status',
        'iqama',
        'name',
        'mobile',
        'email',
        'birth_date',
        'notes',
        'dr_driver_type_id',
    ];

    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
        'name' => 'string',
        'status' => 'integer',
        'mobile' => 'string',
        'birth_date' => 'date',
        'notes' => 'string',
        'iqama' => 'string',
        'dr_driver_type_id' => 'integer',
    ];

    public static array $rules = [
        'name' => 'required|string|max:100',
        'iqama' => 'required|string|max:20|unique:drivers,iqama',
        'mobile' => 'nullable|string|max:15',
        'email' => 'nullable|email|max:255',
        'birth_date' => 'nullable|date',
        'notes' => 'nullable|string|max:255',
        'status' => 'required|integer|in:1,2,3,4',
    ];

    const STATUS_INACTIVE = 1;

    const STATUS_ACTIVE = 2;

    const STATUS_SUSPEND = 3;

    const STATUS_LEAVE = 4;

    public static function statuses(?array $only = null): array
    {
        $statuses = [
            self::STATUS_INACTIVE => __('models/driver.statuses.available'),
            self::STATUS_ACTIVE => __('models/driver.statuses.active'),
            self::STATUS_SUSPEND => __('models/driver.statuses.suspended'),
            self::STATUS_LEAVE => __('models/driver.statuses.leave'),
        ];

        if ($only) {
            return array_intersect_key($statuses, array_flip($only));
        }

        return $statuses;
    }

    public function getStatusTextAttribute(): string
    {
        return self::statuses()[$this->status] ?? (string) $this->status;
    }

    public function getStatusBadgeAttribute(): string
    {
        $badges = [
            self::STATUS_ACTIVE => 'badge badge-success',
            self::STATUS_LEAVE => 'badge badge-warning',
            self::STATUS_INACTIVE => 'badge badge-secondary',
            self::STATUS_SUSPEND => 'badge badge-danger',
        ];

        return $badges[$this->status] ?? 'badge badge-light';
    }

    public function getActiveRefNoAttribute(): ?string
    {
        return $this->activeCompanyReference?->ref_no;
    }

    public function scopeActiveOnly($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeSuspendOnly($query)
    {
        return $query->where('status', self::STATUS_SUSPEND);
    }

    public function scopeInactiveOnly($query)
    {
        return $query->where('status', self::STATUS_INACTIVE);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function companyReferences(): HasMany
    {
        return $this->hasMany(DriverCompanyReference::class, 'driver_id');
    }

    public function activeCompanyReference(): HasOne
    {
        return $this->hasOne(DriverCompanyReference::class, 'driver_id')
            ->where('status', DriverCompanyReference::STATUS_ACTIVE);
    }

    public function settlementDetails(): HasMany
    {
        return $this->hasMany(DrSettlementDetail::class, 'driver_id');
    }

    public function driverType(): BelongsTo
    {
        return $this->belongsTo(DrDriverType::class, 'dr_driver_type_id');
    }
}
