<?php

// namespace Modules\Store\App\Models;

// use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\Factories\HasFactory;

// class ProductInventorySetting extends Model
// {
//     use HasFactory;

//     protected $table = 'product_inventory_settings';

//     protected $fillable = [
//         'product_id',
//         'org_id',
//         'track_quantity',
//         'track_batch',
//         'track_expiry',
//         'allow_backorders',
//         'lead_time_days',

//     ];

//     /*
//     |--------------------------------------------------------------------------
//     | العلاقات
//     |--------------------------------------------------------------------------
//     */

//     // علاقة بالمنتج
//     public function product()
//     {
//         return $this->belongsTo(\App\Models\BasicDataApp\Product::class, 'product_id');
//     }

//     // علاقة بالمؤسسة (إن وجدت)
//     public function organization()
//     {
//         return $this->belongsTo(\App\Models\Organization::class, 'org_id');
//     }

//     // علاقة يمكن أن ترجع الإعدادات العامة
//     public function globalSettings()
//     {
//         return $this->belongsTo(InventorySettings::class, 'org_id', 'org_id');
//     }
// }
