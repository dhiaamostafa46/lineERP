<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('invoice_settings', function (Blueprint $table) {
            $table->boolean('purchase_auto_post')->default(true)->after('sales_auto_post');
            $table->boolean('enable_shipping')->default(true)->after('sales_return_next_number');
            $table->decimal('default_shipping_vat_rate', 5, 2)->default(15.00)->after('enable_shipping');
            $table->boolean('show_product_image')->default(false)->after('show_logo_in_print');
            $table->boolean('show_discount_column')->default(true)->after('show_product_image');
            $table->boolean('show_unit_price_after_vat')->default(false)->after('show_discount_column');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoice_settings', function (Blueprint $table) {
            //
        });
    }
};
