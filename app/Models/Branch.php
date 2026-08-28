<?php

namespace App\Models;

use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Organization;
use App\Models\User;
class Branch extends Model
{
    use SoftDeletes, Translatable;
    protected $table = 'branches';
    protected $fillable = ['user_id', 'phone', 'area', 'city', 'district', 'long', 'lat', 'distance', 'manager', 'description', 'status', 'org_id'];

    public $translatedAttributes = ['name', 'address'];
    /**
     * Get the translations for the branch.
     */

    /**
     * Get the user that owns the branch.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function rules()
    {
        foreach (config('langs') as $locale => $language) {
            $rules[$locale . '.name'] = 'required|string|max:255';
            $rules[$locale . '.address'] = 'required|string|max:255';
        }

        $rules['phone'] = 'required';
        return $rules;
    }
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;

    public static function statuses()
    {
        return [
            self::STATUS_INACTIVE => __('lang.inactive'),
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
            self::STATUS_INACTIVE => 'badge badge-danger',
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


}
