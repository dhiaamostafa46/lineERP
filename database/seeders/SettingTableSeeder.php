<?php

namespace Database\Seeders;

use App\Models\Theme;
use App\Models\Setting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Setting::create([
            'logo' => 'logo_en.png',
            'fav_icon' => 'favicon.png',
            'name' => 'EVIX',
            'coming_soon' => false,
            'count_user' => 2,
            'actual_user' => 10,
        ]);
        if (!DB::table('hr_settings')->first()) {
            $now = now();

            DB::table('hr_settings')->insert([
                'delivery_payroll_at' => '5',
                'preparing_payroll_at' => '25',
                'min_salary' => 3000.0,
                'max_off_days' => 14,
                'currency' => 'SAR',
            ]);
        }

        if (!DB::table('inventory_settings')->first()) {
            $now = now();

            DB::table('inventory_settings')->insert([
                'costing_method' => 'weighted_average',
                'org_id' => 1,
                'allow_negative_stock' => false,
                'auto_calculate_cost' => true,
                'stock_valuation_enabled' => true,
                'auto_serial_number' => true,
                'stock_transfer_prefix' => 'TRF',
                'stocktake_prefix' => 'STK',
            ]);
        }

        if (!DB::table('accounting_settings')->exists()) {
            DB::table('accounting_settings')->insert([
                'currency' => 'SAR',
                'decimal_places' => 2,
                'journal_prefix' => 'JE',
                'journal_next_number' => 1,
                'allow_backdated_entries' => false,
                'allow_future_dated_entries' => false,
                'lock_period_pwd_enabled' => false,
                'lock_period_pwd' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        if (!DB::table('invoice_settings')->exists()) {
            DB::table('invoice_settings')->insert([
                'sales_prefix' => 'INV',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        // if (!DB::table('product_inventory_settings')->first()) {
        //     $now = now();

        //     DB::table('product_inventory_settings')->insert([
        //         'track_quantity' => true,
        //         'track_batch'    => false,
        //         'track_expiry'   => false,
        //         'allow_backorders'=> false,
        //         'lead_time_days'  => 3,
        //         'org_id' => 1,
        //         'product_id' => 1,
        //     ]);
        // }
        Theme::create(['name' => 'default']);
    }
}
