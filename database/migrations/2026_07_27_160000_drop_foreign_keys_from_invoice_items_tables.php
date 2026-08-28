<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $tables = [
            'purchase_invoice_items',
            'purchase_order_items',
            'sales_invoice_items',
            'sales_return_invoice_items',
            'purchase_return_invoice_items',
            'quotation_items',
            'sales_debit_note_items',
        ];

        foreach ($tables as $table) {
            if (!Schema::hasTable($table)) {
                continue;
            }
            $fks = DB::select("SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = 'product_id' AND REFERENCED_TABLE_NAME IS NOT NULL", [$table]);
            foreach ($fks as $fk) {
                try {
                    DB::statement("ALTER TABLE `{$table}` DROP FOREIGN KEY `{$fk->CONSTRAINT_NAME}`");
                } catch (\Throwable $e) {
                    // Ignore if already dropped
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
};
