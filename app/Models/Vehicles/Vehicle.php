<?php

namespace App\Models\Vehicles;

use App\Models\BaseModel;
use App\Models\Project;
use App\Models\StoreApp\Store;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Modules\Vehicles\App\Helpers\PlateHelper;
use Modules\Vehicles\App\Models\vc_delegation;
use Modules\Vehicles\App\Models\vc_vehicle;

class Vehicle extends BaseModel
{
    use \App\Traits\BelongsToBranch;

    use HasFactory, SoftDeletes;

    protected $table = 'vehicles';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['status', 'branch_id', 'store_id', 'project_id', 'vehicle_brand_id', 'vehicle_model_id', 'vehicle_license_id', 'color', 'year', 'plate', 'plate_letters', 'plate_numbers', 'current_mileage', 'notes', 'attributes', 'license_number', 'license_expiry_date', 'license_image', 'vehicle_image', 'license_reg_type'];

    protected $casts = [
        'id' => 'integer',
        'color' => 'string',
        'year' => 'integer',
        'plate' => 'string',
        'plate_letters' => 'string',
        'plate_numbers' => 'string',
        'notes' => 'string',
        'license_reg_type' => 'string',
    ];

    public static array $rules = [
        'plate_letters' => 'required|string|max:50',
        'plate_numbers' => 'required|string|max:50',
        'plate' => 'unique:vehicles,plate',
        'vehicle_brand_id' => 'required',
        'vehicle_model_id' => 'required',
        'color' => 'nullable|string|max:25',
        'year' => 'nullable|string|max:10',
    ];

    const STATUS_AVAILABLE = 1;

    const STATUS_ACTIVE = 2;

    const STATUS_MAINTENANCE = 3;

    const STATUS_OUT_OF_SERVICE = 4;

    const STATUS_SOLD = 5;

    const STATUS_PENDING = 6;

    public static function statuses(?array $only = null)
    {
        $statuses = [
            self::STATUS_AVAILABLE => __('models/vc.statues.available'),
            self::STATUS_ACTIVE => __('models/vc.statues.active'),
            self::STATUS_MAINTENANCE => __('models/vc.statues.mainten'),
            self::STATUS_OUT_OF_SERVICE => __('models/vc.statues.out'),
            self::STATUS_SOLD => __('models/vc.statues.sold'),
            self::STATUS_PENDING => __('models/vc.statues.pending'),
        ];

        if ($only) {
            return array_intersect_key($statuses, array_flip($only));
        }

        return $statuses;
    }

    public function getStatusTextAttribute()
    {
        return self::statuses()[$this->status];
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            self::STATUS_AVAILABLE => 'badge badge-primary',
            self::STATUS_ACTIVE => 'badge badge-success',
            self::STATUS_MAINTENANCE => 'badge badge-warning',
            self::STATUS_OUT_OF_SERVICE => 'badge badge-danger',
            self::STATUS_SOLD => 'badge badge-secondary',
            self::STATUS_PENDING => 'badge badge-warning',
        ];

        return $badges[$this->status];
    }

    public function scopeActiveOnly($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeAvailableOnly($query)
    {
        return $query->where('status', self::STATUS_AVAILABLE);
    }

    public function scopeLicenseExpiringSoon($q, $days = 30)
    {
        return $q->whereDate('license_expiry_date', '<=', now()->addDays($days));
    }

    /**
     * Scope a query to only include inactive Document.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePendingOnly($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    //  public function holidays()
    // {
    //     return $this->hasMany(HrHoliday::class, 'employee_id', 'id');
    // }
    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class, 'vehicle_brand_id', 'id');
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class, 'store_id');
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function vcmodel()
    {
        return $this->belongsTo(vehicleModel::class, 'vehicle_model_id', 'id');
    }

    public function vc_vehicle()
    {
        return $this->hasOne(vc_vehicle::class, 'vehicle_id', 'id');
    }

    public function authorizations()
    {
        return $this->hasMany(vc_delegation::class);
    }

    public function getPlateArabicAttribute()
    {
        //    return PlateHelper::toArabic($this->plate_letters);
        return PlateHelper::toArabic($this->plate_letters)
        .'  '.' '
        .$this->plate_numbers;
    }

    public function getNameAttribute()
    {
        $brand = $this->brand ? $this->brand->name : '';
        $model = $this->vcmodel ? $this->vcmodel->name : '';
        
        $nameParts = array_filter([$brand, $model, $this->year]);
        
        return !empty($nameParts) ? implode(' - ', $nameParts) : null;
    }

    public function getDisplayImageUrlAttribute(): string
    {
        if ($this->vehicle_image) {
            return Storage::disk('public')->url($this->vehicle_image);
        }

        return vc_vehicle::defaultImageUrl($this->vc_vehicle?->vehicle_type);
    }
}
