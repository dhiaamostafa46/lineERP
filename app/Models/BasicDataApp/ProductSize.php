<?php

namespace App\Models\BasicDataApp;

use App\Models\Organization;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductSize extends Model implements TranslatableContract
{
    use HasFactory, Translatable;

    protected $table = 'product_sizes';
    public $translatedAttributes = ['name'];

    protected $fillable = ['product_id', 'sale_price', 'cost_price', 'consumption_factor', 'barcode', 'status'];

    protected $casts = [
        'sale_price' => 'decimal:4',
        'cost_price' => 'decimal:4',
        'consumption_factor' => 'double',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function stocks()
    {
        return $this->hasMany(\App\Models\StoreApp\Stock::class, 'product_id')->where('is_size', true);
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

    protected static function booted()
    {
        static::creating(function ($productSize) {
            if ($productSize->barcode === null || $productSize->barcode === '') {
                $productSize->barcode = self::generateUniqueBarcode();
            }
        });
    }

    public static function generateUniqueBarcode()
    {
        do {
            // Generate a random 13-digit number.
            $barcode = mt_rand(1000000000000, 9999999999999);
        } while (self::where('barcode', $barcode)->exists());

        return $barcode;
    }

    public static function rules()
    {
        $rules = [];

        $rules['consumption_factor'] = 'required|numeric|min:0';
        $rules['sale_price'] = 'required|numeric|min:0';
        $rules['cost_price'] = 'required|numeric|min:0';
        $rules['barcode'] = 'nullable|numeric|min:0';
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
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActiveOnly($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    /**
     * Scope a query to only include inactive units.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeInactiveOnly($query)
    {
        return $query->where('status', self::STATUS_INACTIVE);
    }
}
