<?php

namespace App\Models\Vehicles;

use App\Models\Vehicles\Vehicle;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class vehicleModel extends Model
{
    use SoftDeletes, Translatable;

    public $table = 'vehicle_models';

    public $fillable = [
        'status',
        'brand_id'
    ];

    public $translatedAttributes = ['name'];

    public static function rules()
    {
        foreach (config('langs') as $locale => $language) {
            $rules[$locale . '.name'] = 'required|string|max:255';
        }

        $rules['status']    = 'required';
     

        return $rules;
    }
    
    const STATUS_INACTIVE = 1;
    const STATUS_ACTIVE = 2;

    public static function statuses()
    {
        return [
            self::STATUS_INACTIVE   => __('lang.inactive'),
            self::STATUS_ACTIVE => __('lang.active'),
        ];
    }

    public function getStatusTextAttribute()
    {
        return self::statuses()[$this->status];
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            self::STATUS_INACTIVE   => 'badge badge-danger',
            self::STATUS_ACTIVE => 'badge badge-success',
        ];
        return $badges[$this->status];
    }
    public function getTypeBadgeAttribute()
    {
        $badges = [
            self::STATUS_INACTIVE   => 'badge badge-warning',
            self::STATUS_ACTIVE => 'badge badge-success',
        ];
        return $badges[$this->status];
    }

    /**
     * Scope a query to only include active Document.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActiveOnly($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    /**
     * Scope a query to only include inactive Document.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeInactiveOnly($query)
    {
        return $query->where('status', self::STATUS_INACTIVE);
    }



    public function vehicles(): HasMany
    {
        return $this->hasMany(Vehicle::class, 'vehicle_brand_id');
    }

     public function brands(): BelongsTo
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    // Relations


    // public function owner()
    // {
    //     return $this->belongsTo(HrEmployee::class, 'owner_id' ,'id');
    // }


    // public function jobs()
    // {
    //     return $this->hasManyThrough(
    //         HrJob::class,
    //         HrEmployee::class,
    //         'department_id',
    //         'id',
    //         'id',
    //         'job_id'
    //     );
    // }
}
