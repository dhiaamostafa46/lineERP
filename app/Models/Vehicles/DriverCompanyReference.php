<?php

namespace App\Models\Vehicles;

use App\Models\BaseModel;
use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\Rule;

class DriverCompanyReference extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected static function newFactory(): \Database\Factories\DriverCompanyReferenceFactory
    {
        return \Database\Factories\DriverCompanyReferenceFactory::new();
    }

    public const STATUS_ACTIVE = 'active';

    public const STATUS_COMPLETED = 'completed';

    public const STATUS_SUSPENDED = 'suspended';

    protected $table = 'driver_company_references';

    protected $fillable = [
        'driver_id',
        'company_id',
        'ref_no',
        'status',
        'started_at',
        'ended_at',
    ];

    protected $casts = [
        'id' => 'integer',
        'driver_id' => 'integer',
        'company_id' => 'integer',
        'ref_no' => 'string',
        'status' => 'string',
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
    ];

    public static array $rules = [
        'driver_id' => 'required|integer|exists:drivers,id',
        'company_id' => 'required|integer|exists:companies,id',
        'ref_no' => 'required|string|max:50',
        'status' => 'required|in:active,completed,suspended',
        'started_at' => 'nullable|date',
        'ended_at' => 'nullable|date|after_or_equal:started_at',
    ];

    public static function statuses(?array $only = null): array
    {
        $statuses = [
            self::STATUS_ACTIVE => __('models/driver_company_reference.statuses.active'),
            self::STATUS_COMPLETED => __('models/driver_company_reference.statuses.completed'),
            self::STATUS_SUSPENDED => __('models/driver_company_reference.statuses.suspended'),
        ];

        if ($only) {
            return array_intersect_key($statuses, array_flip($only));
        }

        return $statuses;
    }

    public static function rulesFor(?int $id = null, ?int $companyId = null): array
    {
        $rules = self::$rules;

        $unique = Rule::unique('driver_company_references', 'ref_no');

        if ($companyId !== null) {
            $unique = $unique->where('company_id', $companyId);
        }

        if ($id !== null) {
            $unique = $unique->ignore($id);
        }

        $rules['ref_no'] = ['required', 'string', 'max:50', $unique];

        return $rules;
    }

    public function getStatusTextAttribute(): string
    {
        return self::statuses()[$this->status] ?? $this->status;
    }

    public function getStatusBadgeAttribute(): string
    {
        $badges = [
            self::STATUS_ACTIVE => 'badge badge-success',
            self::STATUS_COMPLETED => 'badge badge-secondary',
            self::STATUS_SUSPENDED => 'badge badge-danger',
        ];

        return $badges[$this->status] ?? 'badge badge-light';
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function scopeActiveOnly($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeCompletedOnly($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    public function scopeSuspendedOnly($query)
    {
        return $query->where('status', self::STATUS_SUSPENDED);
    }

    public function scopeForDriver($query, int $driverId)
    {
        return $query->where('driver_id', $driverId);
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class, 'driver_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }
}
