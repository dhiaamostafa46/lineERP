<?php

namespace App\Models\AccuSoft;

use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TaxAccount extends Model
{
    use  HasFactory, SoftDeletes, Translatable;

    protected $table = 'tax_accounts';

    public $translatedAttributes = ['name'];

    protected $fillable = [
        'rate',
        'status',
        'branch_id',
    ];

    public function branch()
    {
        return $this->belongsTo(\App\Models\Branch::class, 'branch_id');
    }

    protected $casts = [
        'rate' => 'decimal:2',
        'status' => 'integer',
    ];

    const STATUS_INACTIVE = 1;

    const STATUS_ACTIVE = 2;

    public static function rules($id = null)
    {
        $rules = [
            'rate' => 'required|numeric|min:0|max:100',
            'status' => 'required|integer|in:1,2',
        ];

        foreach (config('langs') as $locale => $language) {
            $rules[$locale.'.name'] = 'required|string|max:255';
        }

        return $rules;
    }

    public static function statuses()
    {
        return [
            self::STATUS_ACTIVE => __('lang.active'),
            self::STATUS_INACTIVE => __('lang.inactive'),
        ];
    }

    public function getStatusTextAttribute()
    {
        return self::statuses()[$this->status] ?? __('lang.unknown');
    }

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }
}
