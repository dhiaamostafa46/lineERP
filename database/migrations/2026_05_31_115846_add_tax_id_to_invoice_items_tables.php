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
        $mainTables = ['sales_invoices', 'purchase_invoices', 'quotations'];
        foreach ($mainTables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (!Schema::hasColumn($tableName, 'shipping_tax_id')) {
                    $table->unsignedBigInteger('shipping_tax_id')->nullable()->after('shipping_vat_rate');
                }
            });
        }

        $itemTables = ['sales_invoice_items', 'purchase_invoice_items', 'quotation_items', 'purchase_order_items'];
        foreach ($itemTables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (!Schema::hasColumn($tableName, 'tax_id')) {
                    $table->unsignedBigInteger('tax_id')->nullable()->after('vat_rate');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $mainTables = ['sales_invoices', 'purchase_invoices', 'quotations'];
        foreach ($mainTables as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->dropColumn('shipping_tax_id');
            });
        }

        $itemTables = ['sales_invoice_items', 'purchase_invoice_items', 'quotation_items', 'purchase_order_items'];
        foreach ($itemTables as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->dropColumn('tax_id');
            });
        }
    }
};
