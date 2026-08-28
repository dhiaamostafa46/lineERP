<?php

namespace Modules\Store\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InventorySettings extends Model
{
    use HasFactory;

    protected $table = 'inventory_settings';

    protected $fillable = [
        'org_id',
        'costing_method',
        'default_transfer_type',
        'allow_negative_stock',
        'auto_calculate_cost',
        'stock_valuation_enabled',
        'auto_serial_number',
        'stock_transfer_prefix',
        'stocktake_prefix',
      
    ];




    public const COSTING_METHOD_FIFO = 'fifo';
    public const COSTING_METHOD_LIFO = 'lifo';
    public const COSTING_METHOD_WEIGHTED_AVERAGE = 'weighted_average';
    public const COSTING_METHOD_STANDARD = 'standard';

    // $table->enum('costing_method', ['fifo', 'lifo', 'weighted_average', 'standard'])
    //       ->default('weighted_average');


    public static function getCostingMethods()
    {
        return [
            self::COSTING_METHOD_FIFO => __('store::lang.fifo'),
            self::COSTING_METHOD_LIFO => __('store::lang.lifo'),
            self::COSTING_METHOD_WEIGHTED_AVERAGE => __('store::lang.weighted_average'),
            self::COSTING_METHOD_STANDARD => __('store::lang.standard'),
        ];
    }



    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function organization()
    {
        return $this->belongsTo(\App\Models\Organization::class, 'org_id');
    }
}
