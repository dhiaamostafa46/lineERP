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
            $table->string('sales_debit_prefix')->nullable()->after('sales_return_next_number');
            $table->integer('sales_debit_next_number')->default(1)->after('sales_debit_prefix');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoice_settings', function (Blueprint $table) {
            $table->dropColumn(['sales_debit_prefix', 'sales_debit_next_number']);
        });
    }
};
