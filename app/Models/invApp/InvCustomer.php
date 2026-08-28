<?php

namespace App\Models\invApp;

use App\Helpers\ImageUploaderTrait;
use App\Models\AccuSoft\TreeAccounts;
use App\Models\Branch;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\File;
use App\Models\invApp\SalesInvoice;

class InvCustomer extends Model
{
    use  ImageUploaderTrait, SoftDeletes, Translatable;

    protected $table = 'inv_customers';

    protected $fillable = [
        'phone',
        'email',
        'vat_number',
        'cr_number',
        'country',
        'city',
        'district',
        'street',
        'building_number',
        'postal_code',
        'additional_number',
        'tree_account_id',
        'branch_id',
        'credit_limit',
        'status',
        'file',
    ];

    public function setFileAttribute($file)
    {
        try {
            if ($file) {
                // حذف الملف القديم إذا كان موجوداً
                if ($this->file) {
                    $this->deleteFile($this->file, 'customers');
                }

                // إنشاء اسم الملف
                $fileName = $this->createFileName($file);

                // حفظ الملف في مجلد Holiday
                $this->saveFileType($file, $fileName, 'customers');

                // حفظ اسم الملف في قاعدة البيانات
                $this->attributes['file'] = $fileName;
            }
        } catch (\Throwable $th) {

            $this->attributes['file'] = null;
        }
    }

    /**
     * الحصول على مسار الملف الكامل
     */
    public function getFileOriginalPathAttribute()
    {
        if ($this->file && File::exists('uploads/images/customers/'.$this->file)) {
            return 'uploads/images/customers/'.$this->file;
        }

        return null;
    }

    /**
     * الحصول على رابط الملف
     */
    public function getFileUrlAttribute()
    {
        return $this->file_original_path ? asset($this->file_original_path) : null;
    }

    /**
     * الحصول على معلومات الملف
     */
    public function getFileInfoAttribute()
    {
        $path = 'uploads/images/customers/'.$this->attachment;

        if (! $this->attachment || ! File::exists($path)) {
            return null;
        }

        return [
            'name' => $this->attachment,
            'path' => $path,
            'size' => File::size($path),
            'extension' => File::extension($path),
            'mime' => File::mimeType($path),
            'url' => asset($path),
        ];
    }

    public $translationModel = InvCustomerTranslation::class;

    public $translatedAttributes = ['name'];

    protected $casts = [
        'status' => 'boolean',
        'credit_limit' => 'decimal:4',
    ];

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
        return $this->statuses()[$this->status];

    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            self::STATUS_INACTIVE => 'badge badge-danger',
            self::STATUS_ACTIVE => 'badge badge-success',
        ];

        return $badges[$this->status];
    }

    public function treeAccount()
    {
        return $this->belongsTo(TreeAccounts::class, 'tree_account_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function scopeActiveOnly($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    /**
     * العلاقة مع فواتير المبيعات
     */
    public function salesInvoices()
    {
        return $this->hasMany(SalesInvoice::class, 'customer_id');
    }
}

