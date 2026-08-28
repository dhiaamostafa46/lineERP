<?php

namespace Modules\HR\App\Models;

use Modules\HR\App\Models\HrAssetType;
use Illuminate\Database\Eloquent\Model;
use Modules\HR\App\Models\HrDepartment;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\SoftDeletes;

class HrAsset extends Model
{
    use SoftDeletes, Translatable;
    public $table = 'hr_assets';

    const STATUS_INACTIVE = 1;
    const STATUS_ACTIVE = 2;

    public $fillable = [
        'department_id',
        'type_id',
        'is_new',
        'note',
        'status'
    ];

    public $translatedAttributes = ['name'];

    protected $casts = [
        'id' => 'integer',
        'department_id' => 'integer',
        'type_id' => 'integer',
        'is_new' => 'boolean',
        'name' => 'string',
        'note' => 'string',
        'status' => 'integer'
    ];

    public static function rules()
    {
        foreach (config('langs') as $locale => $language) {
            $rules[$locale . '.name'] = 'required|string|max:255';
        }
        $rules['status'] = 'required';
        $rules['is_new'] = 'required';
        $rules['department_id'] = 'required';
        $rules['type_id'] = 'required';
        $rules['note'] = 'nullable|string|max:255';
        return $rules;
    }

    // status array
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


    // Relations
    public function type()
    {
        return $this->belongsTo(HrAssetType::class, 'type_id', 'id');
    }

    public function department()
    {
        return $this->belongsTo(HrDepartment::class, 'department_id', 'id');
    }

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

    public function financialAsset()
    {
        return $this->morphOne(\Modules\AccuSoft\App\Models\Asset::class, 'assetable');
    }

    public function shouldCreateFinancialAsset(): bool
    {
        // Depends on the specific rules of the company, usually handled via observers or controllers
        return true; 
    }

}
