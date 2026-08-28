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
        Schema::table('purchase_invoices', function (Blueprint $table) {
            $table->decimal('shipping_cost', 15, 4)->default(0)->after('total_inclusive_vat');
            $table->decimal('shipping_vat_rate', 5, 2)->default(0)->after('shipping_cost');
            $table->decimal('shipping_vat_amount', 15, 4)->default(0)->after('shipping_vat_rate');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_invoices', function (Blueprint $table) {
            $table->dropColumn(['shipping_cost', 'shipping_vat_rate', 'shipping_vat_amount']);
        });
    }
};
