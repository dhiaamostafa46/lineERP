<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Add fields to sales_invoice_zatcas
        Schema::table('sales_invoice_zatcas', function (Blueprint $table) {
            $table->string('uuid')->nullable()->after('sales_invoice_id');
            $table->integer('icv')->nullable()->after('uuid');
            $table->text('previous_invoice_hash')->nullable()->after('icv');
        });

        // 2. Remove fields from sales_invoices
        Schema::table('sales_invoices', function (Blueprint $table) {
            $table->dropColumn(['uuid', 'icv', 'previous_invoice_hash']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. Re-add fields to sales_invoices
        Schema::table('sales_invoices', function (Blueprint $table) {
            $table->string('uuid')->nullable()->after('id');
            $table->integer('icv')->nullable()->after('parent_id');
            $table->text('previous_invoice_hash')->nullable()->after('icv');
        });

        // 2. Remove fields from sales_invoice_zatcas
        Schema::table('sales_invoice_zatcas', function (Blueprint $table) {
            $table->dropColumn(['uuid', 'icv', 'previous_invoice_hash']);
        });
    }
}
;
