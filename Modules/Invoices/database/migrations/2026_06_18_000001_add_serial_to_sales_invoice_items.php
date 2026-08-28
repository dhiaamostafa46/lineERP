<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('sales_invoice_items', function (Blueprint $table) {
            $table->string('serial', 6)->nullable()->after('product_id')->index();
            $table->unique(['sales_invoice_id', 'serial'], 'sii_invoice_serial_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales_invoice_items', function (Blueprint $table) {
            $table->dropUnique('sii_invoice_serial_unique');
            $table->dropColumn('serial');
        });
    }
};
