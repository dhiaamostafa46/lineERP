<?php

namespace App\Models\BasicDataApp;

use App\Helpers\ImageUploaderTrait;
use App\Models\Organization;
use App\Models\User;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
      
    use  HasFactory, ImageUploaderTrait, SoftDeletes, Translatable;

    protected $table = 'categories';

    protected $fillable = ['org_id', 'branch_id', 'user_id', 'parent_id', 'type', 'status', 'img', 'sort', 'is_virtual'];

    public $translatedAttributes = ['name'];

    /**
     * الثوابت
     */
    const TYPE_Visible = 1;      // تُعرض في المبيعات

    const TYPE_Hidden = 0;      // لا تُعرض في المبيعات

    const STATUS_INACTIVE = 0;

    const STATUS_ACTIVE = 1;

    const VIRTUAL_FALSE = 0;  // فئة حقيقية

    const VIRTUAL_TRUE = 1;   // فئة افتراضية

    /**
     * علاقات
     */
    public function user()
    {
        return $this->belongsTo(User::class)->withDefault();
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class, 'org_id')->withDefault();
    }

    public function branch()
    {
        return $this->belongsTo(\App\Models\Branch::class, 'branch_id');
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    /**
     * Global Scope لإظهار الفئات النشطة فقط
     */
    protected static function booted()
    {
        // static::addGlobalScope('active', function ($query) {
        //     $query->where('status', self::STATUS_ACTIVE);
        // });
    }

    /**
     * Mutators
     */
    public function setOrgIDAttribute($value)
    {
        $this->attributes['org_id'] = $value ?? 0;
    }

    public function setUserIdAttribute($value)
    {
        $this->attributes['user_id'] = $value ?? auth()->id();
    }

    public function setSortAttribute($value)
    {
        $this->attributes['sort'] = ($value === null || $value === '') ? 1 : $value;
    }

    public function setImgAttribute($file)
    {
        try {
            if ($file) {
                // حذف الصورة القديمة
                if ($this->img) {
                    $this->deleteFile($this->img, 'categories');
                }

                // إنشاء اسم جديد للصورة
                $fileName = $this->createFileName($file);

                // حفظ الصورة في مجلد categories
                $this->saveFileType($file, $fileName, 'categories');

                // حفظ الاسم في قاعدة البيانات
                $this->attributes['img'] = $fileName;
            }
        } catch (\Throwable $th) {
            $this->attributes['img'] = null;
        }
    }

    /**
     * Accessors
     */
    public function getImgPathAttribute()
    {
        return $this->img ? asset('uploads/images/categories/'.$this->img) : asset('uploads/images/categories/no_img.jpg');
    }

    public function getImgThumbPathAttribute()
    {
        return $this->img ? asset('uploads/images/categories/'.$this->img) : asset('uploads/images/categories/no_img.jpg');
    }

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

    public function getIsVirtualTextAttribute()
    {
        return self::virtualStatuses()[$this->is_virtual] ?? __('lang.unknown');
    }

    public function getIsVirtualBadgeAttribute()
    {
        $badges = [
            self::VIRTUAL_FALSE => 'badge badge-primary',
            self::VIRTUAL_TRUE => 'badge badge-warning',
        ];

        return $badges[$this->is_virtual] ?? 'badge badge-secondary';
    }

    public function getTypeTextAttribute()
    {
        return self::types()[$this->type] ?? __('lang.unknown');
    }

    /**
     * قواعد التحقق
     */
    public static function rules()
    {
        $rules = [];
        foreach (config('langs') as $locale => $language) {
            $rules[$locale.'.name'] = 'required|string|max:255';
        }
        $rules['parent_id'] = 'nullable|exists:categories,id';
        $rules['sort'] = 'nullable|numeric|min:1';

        return $rules;
    }

    /**
     * الحالات
     */
    public static function statuses()
    {
        return [
            self::STATUS_INACTIVE => __('lang.inactive'),
            self::STATUS_ACTIVE => __('lang.active'),
        ];
    }

    public static function virtualStatuses()
    {
        return [
            self::VIRTUAL_FALSE => __('lang.real'),    // فئة حقيقية
            self::VIRTUAL_TRUE => __('lang.virtual'), // فئة افتراضية
        ];
    }

    public static function types()
    {
        return [
            self::TYPE_Visible => __('basicdata::models/db_categories.type_statuses.Visible'),   // تُعرض
            self::TYPE_Hidden => __('basicdata::models/db_categories.type_statuses.Hidden'),    // لا تُعرض
        ];
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeActiveOnly($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeInactive($query)
    {
        return $query->where('status', self::STATUS_INACTIVE);
    }

    public function scopeVirtual($query)
    {
        return $query->where('is_virtual', self::VIRTUAL_TRUE);
    }

    public function scopeReal($query)
    {
        return $query->where('is_virtual', self::VIRTUAL_FALSE);
    }

    public function scopeByOrganization($query, $orgId)
    {
        return $query->where('org_id', $orgId);
    }
}
