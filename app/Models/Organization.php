<?php

namespace App\Models;

use App\Helpers\ImageUploaderTrait;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Organization extends Model
{
    use HasFactory, SoftDeletes, Translatable, ImageUploaderTrait;

    protected $fillable = [
        'CR', // السجل التجاري
        'logo', // الشعار
        'signature', // التوقيع
        'status', // الحالة
        'activity', // النشاط
        'is_new', // جديد
        'is_paid', // مدفوع
        'pay_gate_status', // حالة بوابة الدفع
        'insurance_sub_no', // رقم إشتراك بالتأمين
        'chamber_no', // رقم الغرفة التجارية
        'organization_number', // رقم المنشأة
        'tax_registration_type',
        'national_address', // العنوان الوطني
        'tax_number', // الرقم الضريبي
        'seal',
    ];



    public static function rules()
    {
        foreach (config('langs') as $locale => $language) {
            $rules[$locale . '.name'] = 'required|string|max:255';

        }

        $rules['CR'] = 'required';

        return $rules;
    }

    public $translatedAttributes = ['name'];
    /**
     * Get the translations for the organization.
     */
    public function translations()
    {
        return $this->hasMany(OrganizationTranslation::class);
    }

    /**
     * Get the specific translation for the given locale.
     */
    public function translation($locale = null)
    {
        $locale = $locale ?: app()->getLocale();
        return $this->hasOne(OrganizationTranslation::class)->where('locale', $locale);
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






     // logo
     public function setLogoAttribute($file)
     {
         try {
             if ($file) {

                 $fileName = $this->createFileName($file);

                 $this->logoImage($file, $fileName);

                 $this->thumbImage($file, $fileName, 200, 200);

                 $this->attributes['logo'] = $fileName;
             }
         } catch (\Throwable $th) {
             $this->attributes['logo'] = $file;
         }
     }


     public function setsealAttribute($file)
     {
         try {
             if ($file) {

                 $fileName = $this->createFileName($file);

                 $this->logoImage($file, $fileName);

                 $this->thumbImage($file, $fileName, 200, 200);

                 $this->attributes['seal'] = $fileName;
             }
         } catch (\Throwable $th) {
             $this->attributes['seal'] = $file;
         }
     }

     public function getLogoOriginalPathAttribute()
     {
         return $this->logo ? asset('uploads/images/logo/' . $this->logo) : "";
     }


     public function getSealOriginalPathAttribute()
     {
         return $this->seal ? asset('uploads/images/logo/' . $this->seal) : asset('admin_assets'). '/media/logos/stampevix.webp' ;
     }













      // signature
    public function setSignatureAttribute($file)
    {
        try {
            if ($file) {

                $fileName = $this->createFileName($file);

                $this->signatureImage($file, $fileName);

                $this->thumbImage($file, $fileName, 200, 200);

                $this->attributes['signature'] = $fileName;
            }
        } catch (\Throwable $th) {
            $this->attributes['signature'] = $file;
        }
    }

    public function getSignatureOriginalPathAttribute()
    {
        return $this->signature ? asset('uploads/images/signature/' . $this->signature) : "";
    }







}
