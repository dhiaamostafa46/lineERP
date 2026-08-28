<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pos_devices', function (Blueprint $table) {
            $table->unsignedBigInteger('default_customer_id')->nullable();
            $table->unsignedBigInteger('shortage_account_id')->nullable();
            $table->unsignedBigInteger('overage_account_id')->nullable();
            $table->unsignedBigInteger('main_safe_account_id')->nullable();
            $table->unsignedBigInteger('sales_account_id')->nullable();
            $table->unsignedBigInteger('discount_account_id')->nullable();
            $table->unsignedBigInteger('vat_account_id')->nullable();
            $table->unsignedBigInteger('cogs_account_id')->nullable();
            $table->unsignedBigInteger('inventory_account_id')->nullable();
            
            $table->boolean('auto_journal_entry')->default(true);
            $table->boolean('allow_negative_stock')->default(false);
            $table->boolean('auto_print_receipt')->default(true);
            $table->boolean('auto_open_drawer')->default(true);
            $table->boolean('allow_price_modification')->default(true);
            $table->boolean('allow_discount_modification')->default(true);
            $table->decimal('max_discount_percent', 5, 2)->default(100.00);
            $table->boolean('show_available_qty')->default(true);
            $table->boolean('enable_pos_returns')->default(true);
            $table->integer('print_copies_count')->default(1);
            $table->integer('session_timeout_minutes')->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('pos_devices', function (Blueprint $table) {
            $table->dropColumn([
                'default_customer_id', 'shortage_account_id', 'overage_account_id', 
                'main_safe_account_id', 'sales_account_id', 'discount_account_id', 
                'vat_account_id', 'cogs_account_id', 'inventory_account_id',
                'auto_journal_entry', 'allow_negative_stock', 'auto_print_receipt',
                'auto_open_drawer', 'allow_price_modification', 'allow_discount_modification',
                'max_discount_percent', 'show_available_qty', 'enable_pos_returns',
                'print_copies_count', 'session_timeout_minutes'
            ]);
        });
    }
};
