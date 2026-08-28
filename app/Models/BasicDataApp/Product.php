<?php

namespace App\Models\BasicDataApp;

use App\Helpers\ImageUploaderTrait;
use App\Models\Organization;
use App\Models\StoreApp\Stock;
use App\Models\StoreApp\StockMovement;
use App\Models\User;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model implements TranslatableContract
{
    use \App\Traits\BelongsToBranch;
    use  HasFactory, ImageUploaderTrait, SoftDeletes, Translatable;

    public $translatedAttributes = ['name', 'details'];

    protected $table = 'products';

    protected $fillable = ['org_id', 'branch_id', 'user_id', 'barcode', 'min_quantity', 'type', 'category_id', 'kitchen_id', 'base_unit_id', 'tax_id', 'cost_price', 'prod_price', 'vat', 'calories', 'img', 's_from', 's_to', 'work_days', 'have_sizes', 'status'];

    protected $casts = [
        'cost_price' => 'decimal:4',
        'prod_price' => 'decimal:4',
        'vat' => 'decimal:2',
        'calories' => 'decimal:2',
        'have_sizes' => 'boolean',
        'work_days' => 'array',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function branch()
    {
        return $this->belongsTo(\App\Models\Branch::class, 'branch_id');
    }

    public function taxAccount()
    {
        return $this->belongsTo(\App\Models\AccuSoft\TaxAccount::class, 'tax_id');
    }

    public function setCategoryIdAttribute($value)
    {
        if (empty($value)) {
            $firstCategory = Category::first();
            $value = $firstCategory ? $firstCategory->id : null;
        }
        $this->attributes['category_id'] = $value;
    }

    public function setTaxIdAttribute($value)
    {
        if (empty($value)) {
            $defaultTax = \App\Models\AccuSoft\TaxAccount::Active()->first();
            $value = $defaultTax ? $defaultTax->id : null;
        }
        $this->attributes['tax_id'] = $value;
    }

    public function setImgAttribute($file)
    {
        try {
            if ($file) {
                // حذف الصورة القديمة
                if ($this->img) {
                    $this->deleteFile($this->img, 'products');
                }

                // إنشاء اسم جديد للصورة
                $fileName = $this->createFileName($file);

                // حفظ الصورة في مجلد categories
                $this->saveFileType($file, $fileName, 'products');

                // حفظ الاسم في قاعدة البيانات
                $this->attributes['img'] = $fileName;
            }
        } catch (\Throwable $th) {
            $this->attributes['img'] = null;
        }
    }

    //  protected static function booted()
    // {
    //     static::creating(function ($product) {
    //         if ($product->barcode === null || $product->barcode === '') {
    //             $product->barcode = self::generateUniqueBarcode();
    //         }
    //     });
    // }

    public static function generateUniqueBarcode()
    {
        do {
            // Generate a random 13-digit number.
            $barcode = mt_rand(1000000000000, 9999999999999);
        } while (self::where('barcode', $barcode)->exists());

        return $barcode;
    }

    /**
     * Accessors
     */
    public function getImgPathAttribute()
    {
        return $this->img ? asset('uploads/images/products/'.$this->img) : asset('uploads/images/products/no_img.jpg');
    }

    public function getImgThumbPathAttribute()
    {
        return $this->img ? asset('uploads/images/products/'.$this->img) : asset('uploads/images/products/no_img.jpg');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function baseUnit()
    {
        return $this->belongsTo(Unit::class, 'base_unit_id');
    }

    // public function saleUnit()
    // {
    //     return $this->belongsTo(Unit::class, 'sale_unit_id');
    // }

    // public function purchaseUnit()
    // {
    //     return $this->belongsTo(Unit::class, 'purchase_unit_id');
    // }

    public function units()
    {
        return $this->hasMany(ProductUnit::class);
    }

    public function sizes()
    {
        return $this->hasMany(ProductSize::class);
    }

    public function stocks()
    {
        return $this->hasMany(Stock::class, 'product_id')->where('is_size', false);
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    const VAT_0 = 0;

    const VAT_15 = 15;

    const VAT_100 = 100;

    const VAT_50 = 50;

    public static function vats()
    {
        return [
            self::VAT_0 => '0 %',
            self::VAT_15 => '15 %',
            self::VAT_100 => '100 %',
        ];
    }

    const TYPE_SALE = 1;

    const TYPE_SERVICE = 2;

    const TYPE_VARIABLE = 3;

    public static function types()
    {
        return [
            self::TYPE_SALE => __('basicdata::models/db_products.fields.product'),
            self::TYPE_SERVICE => __('basicdata::models/db_products.fields.service'),
            self::TYPE_VARIABLE => __('basicdata::models/db_products.sizes'),
        ];
    }

    public function getTypeTextAttribute()
    {
        return self::types()[$this->type];
    }

    const STATUS_INACTIVE = 0;

    const STATUS_ACTIVE = 1;

    /**
     * Get the organization that owns the unit.
     */
    public function organization()
    {
        return $this->belongsTo(Organization::class, 'org_id');
    }

    public static function rules()
    {
        $rules = [];
        // حقول متعددة اللغات
        foreach (config('langs') as $locale => $language) {
            $rules[$locale.'.name'] = 'required|string|max:255';
        }
        // حقول عامة غير مرتبطة باللغات
        $rules['cost_price'] = 'required|numeric|min:0';
        $rules['prod_price'] = 'required|numeric|min:0';
        $rules['category_id'] = 'required|exists:categories,id';
        $rules['vat'] = 'nullable|numeric|min:0|max:100';
        $rules['tax_id'] = 'nullable|exists:tax_accounts,id';

        return $rules;
    }

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

    public function getHaveSizesTextAttribute()
    {
        return $this->have_sizes ? __('lang.yes') : __('lang.no');
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
     * Scope a query to only include active units.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActiveOnly($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    /**
     * Scope a query to only include inactive units.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeInactiveOnly($query)
    {
        return $query->where('status', self::STATUS_INACTIVE);
    }

    protected static function booted()
    {
        static::creating(function ($product) {
            if ($product->barcode === null || $product->barcode === '') {
                $product->barcode = self::generateUniqueBarcode();
            }
        });

        // إضافة هذا الجزء لإنشاء المخزون تلقائياً
        static::created(function ($product) {
            $product->createDefaultStock();
        });
    }

    /**
     * إنشاء سجلات مخزون افتراضية للمنتج الجديد
     */
    public function createDefaultStock()
    {
        // الحصول على المخازن الافتراضية للمنظمة
        // $defaultDepots = \App\Models\Depot::where('org_id', $this->org_id)->where('is_active', true)->get();

        // foreach ($defaultDepots as $depot) {
        //     // إنشاء سجل مخزون بصفر
        //     \App\Models\ProductStock::create([
        //         'org_id' => $this->org_id,
        //         'branch_id' => $depot->branch_id,
        //         'depot_id' => $depot->id,
        //         'product_id' => $this->id,
        //         'unit_id' => $this->base_unit_id, // تحتاج لإضافة base_unit_id
        //         'quantity' => 0,
        //         'average_cost' => $this->cost_price ?? 0,
        //         'min_quantity' => $this->min_quantity ?? 0,
        //         'is_active' => true,
        //     ]);
        // }
    }
}
