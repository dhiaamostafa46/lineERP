<?php

namespace Modules\BasicData\App\Models;

use App\Models\Branch;
use App\Models\BasicDataApp\Product;
use App\Models\Organization;
use App\Models\User;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Validation\Rule;

class DbKitchen extends Model implements TranslatableContract
{
    use HasFactory, Translatable;

    protected $table = 'db_kitchens';

    public $translatedAttributes = ['name'];
    public $translationForeignKey = 'kitchen_id';

    protected $fillable = [
        'orgID',
        'branchID',
        'userID',
        'barcode',
        'status',
    ];

    // ثوابت الحالة
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;

    protected static function booted()
    {
        static::creating(function ($kitchen): void {
            $kitchen->userID = $kitchen->userID ?? auth()->id();
        });
    }

    /**
     * علاقات
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'userID')->withDefault();
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class, 'orgID')->withDefault();
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branchID')->withDefault();
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'kitchen_id');
    }


    /**
     * Accessors & Mutators
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


    /**
     * Static Methods
     */
    public static function rules()
    {
        $rules = [
            'branchID' => 'nullable|exists:branches,id',
            'barcode' => 'nullable|string|max:255',
        ];

        foreach (config('langs') as $locale => $language) {
            $rules[$locale . '.name'] = 'required|string|max:255';
        }
        return $rules;
    }

    public static function statuses()
    {
        return [
            self::STATUS_INACTIVE => __('lang.inactive'),
            self::STATUS_ACTIVE => __('lang.active'),
        ];
    }


    /**
     * Scopes
     */

}
