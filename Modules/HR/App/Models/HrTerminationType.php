<?php

namespace Modules\HR\App\Models;

use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\HR\App\Models\HrTerminationTypeReward;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HrTerminationType extends Model
{
    use SoftDeletes, Translatable;

    public $table = 'hr_termination_types';

    public $fillable = ['status'];

    protected $translationForeignKey = 'type_id';
    public $translatedAttributes = ['name'];

    public $rules = [];

    public static function rules()
    {
        foreach (config('langs') as $locale => $language) {
            $rules[$locale . '.name'] = 'required|string|max:255';
        }

        $rules['status'] = 'required';
        $rules['rewards'] = 'required|array';
        $rules['rewards.*.worked_days'] = 'required';
        $rules['rewards.*.fixed_amount'] = 'required_if:rewards.*.percentage,0';
        $rules['rewards.*.percentage'] = 'required_if:rewards.*.fixed_amount,0';


        return $rules;
    }

    const STATUS_INACTIVE = 1;
    const STATUS_ACTIVE   = 2;

    public static function statuses()
    {
        return [
            self::STATUS_INACTIVE => trans('hr::models/hr_termination_types.fields.inactive'),
            self::STATUS_ACTIVE   => trans('hr::models/hr_termination_types.fields.active')
        ];
    }

    // Accessors
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

    /**
     * Get all of the rewards for the HrTerminationType
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function rewards(): HasMany
    {
        return $this->hasMany(HrTerminationTypeReward::class, 'termination_type_id');
    }
}
