<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_settings', function (Blueprint $table) {
            $table->id();
             $table->unsignedBigInteger('org_id');
             $table->string('costing_method')->default('weighted_average');
            // طرق التقييم
            // $table->enum('costing_method', ['fifo', 'lifo', 'weighted_average', 'standard'])
            //       ->default('weighted_average');

            // إعدادات المخزون
            $table->boolean('allow_negative_stock')->default(false);
            $table->boolean('auto_calculate_cost')->default(true);
            $table->boolean('stock_valuation_enabled')->default(true);

            // إعدادات الحركات
            $table->boolean('auto_serial_number')->default(true);
            $table->string('stock_transfer_prefix')->default('ST');
            $table->string('stocktake_prefix')->default('INV');

            $table->timestamps();
        });

        Schema::create('product_inventory_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('org_id');
            $table->unsignedBigInteger('product_id');
            $table->boolean('track_quantity')->default(true);
            $table->boolean('track_batch')->default(false);
            $table->boolean('track_expiry')->default(false);
            $table->boolean('allow_backorders')->default(false);
            $table->integer('lead_time_days')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_inventory_settings');
        Schema::dropIfExists('inventory_settings');
    }
};
