<?php

namespace Modules\Store\database\seeders;

use Illuminate\Database\Seeder;
use Modules\Store\App\Models\InventorySettings;
use Modules\Store\App\Models\ProductInventorySetting;

class SettingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // إنشاء إعدادات المخزون العامة
        InventorySettings::firstOrCreate(
            ['org_id' => 1],
            [
                'costing_method'         => 'weighted_average',
                'allow_negative_stock'   => false,
                'auto_calculate_cost'    => true,
                'stock_valuation_enabled'=> true,
                'auto_serial_number'     => true,
                'stock_transfer_prefix'  => 'TRF',
                'stocktake_prefix'       => 'STK',

            ]
        );

        // مثال إعداد منتج واحد (إن أردت)
        ProductInventorySetting::firstOrCreate(
            ['product_id' => 1, 'org_id' => 1],
            [
                'track_quantity' => true,
                'track_batch'    => false,
                'track_expiry'   => false,
                'allow_backorders'=> false,
                'lead_time_days'  => 3,

            ]
        );
    }
}
   