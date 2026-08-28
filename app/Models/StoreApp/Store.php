<?php

namespace App\Models\StoreApp;

use App\Models\AccuSoft\TreeAccounts;
use App\Models\Organization;
use App\Models\User;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Store extends Model
{
    use \App\Traits\BelongsToBranch;

    use HasFactory, SoftDeletes, Translatable;

    protected $table = 'stores';

    protected $fillable = [
        'org_id',
        'branch_id',
        'manager_user_id',
        'type',
        'location',
        'status',
        'conversion_factor',
        'tree_account_id',
    ];

    public $translatedAttributes = ['name', 'address'];

    // ثوابت الحالة
    const STATUS_INACTIVE = 0;

    const STATUS_ACTIVE = 1;

    // ثوابت نوع المخزن
    const TYPE_MAIN = 'main';

    const TYPE_SECONDARY = 'secondary';

    /*
    |--------------------------------------------------------------------------
    | العلاقات
    |--------------------------------------------------------------------------
    */

    public function branch()
    {
        return $this->belongsTo(\App\Models\Branch::class, 'branch_id');
    }

    public function managerUser()
    {
        return $this->belongsTo(User::class, 'manager_user_id');
    }

    public function treeAccount()
    {
        return $this->belongsTo(TreeAccounts::class, 'tree_account_id');
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class, 'org_id')->withDefault();
    }

    /*
    |--------------------------------------------------------------------------
    | Mutators
    |--------------------------------------------------------------------------
    */

    public function setOrgIdAttribute($value)
    {
        $this->attributes['org_id'] = $value ?? 0;
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getStatusTextAttribute()
    {
        return self::statuses()[$this->status] ?? __('lang.unknown');
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            self::STATUS_INACTIVE => 'badge badge-danger',
            self::STATUS_ACTIVE => 'badge badge-success',
        ];

        return $badges[$this->status] ?? 'badge badge-secondary';
    }

    public function getTypeTextAttribute()
    {
        return self::types()[$this->type] ?? __('lang.unknown');
    }

    public function getTypeBadgeAttribute()
    {
        $badges = [
            self::TYPE_MAIN => 'badge badge-primary',
            self::TYPE_SECONDARY => 'badge badge-warning',

        ];

        return $badges[$this->type] ?? 'badge badge-secondary';
    }

    /*
    |--------------------------------------------------------------------------
    | قواعد التحقق
    |--------------------------------------------------------------------------
    */

    public static function rules()
    {
        $rules = [];
        foreach (config('langs') as $locale => $language) {
            $rules[$locale.'.name'] = 'required|string|max:255';
        }
        $rules['branch_id'] = 'required|exists:branches,id';
        $rules['manager_user_id'] = 'nullable|exists:users,id';

        return $rules;
    }

    /*
    |--------------------------------------------------------------------------
    | الحالات
    |--------------------------------------------------------------------------
    */

    public static function statuses()
    {
        return [
            self::STATUS_INACTIVE => __('lang.inactive'),
            self::STATUS_ACTIVE => __('lang.active'),
        ];
    }

    public static function types()
    {
        return [
            self::TYPE_MAIN => __('lang.main'),
            self::TYPE_SECONDARY => __('lang.secondary'),

        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeActiveOnly($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeInactiveOnly($query)
    {
        return $query->where('status', self::STATUS_INACTIVE);
    }

    public function scopeByOrganization($query, $orgId)
    {
        return $query->where('org_id', $orgId);
    }
}
