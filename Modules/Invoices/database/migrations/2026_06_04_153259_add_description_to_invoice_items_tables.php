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
        Schema::table('sales_invoice_items', function (Blueprint $table) {
            $table->text('description')->nullable()->after('product_name');
        });

        Schema::table('purchase_invoice_items', function (Blueprint $table) {
            $table->text('description')->nullable()->after('product_name');
        });

        Schema::table('purchase_order_items', function (Blueprint $table) {
            $table->text('description')->nullable()->after('product_name');
        });

        Schema::table('quotation_items', function (Blueprint $table) {
            $table->text('description')->nullable()->after('product_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales_invoice_items', function (Blueprint $table) {
            $table->dropColumn('description');
        });

        Schema::table('purchase_invoice_items', function (Blueprint $table) {
            $table->dropColumn('description');
        });

        Schema::table('purchase_order_items', function (Blueprint $table) {
            $table->dropColumn('description');
        });

        Schema::table('quotation_items', function (Blueprint $table) {
            $table->dropColumn('description');
        });
    }
};
