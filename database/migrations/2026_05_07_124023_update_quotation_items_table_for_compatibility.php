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
        Schema::table('quotation_items', function (Blueprint $table) {
            // Check if column exists before adding to avoid errors on retry
            if (!Schema::hasColumn('quotation_items', 'type_discount')) {
                $table->integer('type_discount')->default(1)->after('unit_price');
            }
            if (!Schema::hasColumn('quotation_items', 'number_discount')) {
                $table->decimal('number_discount', 20, 4)->default(0)->after('type_discount');
            }
            if (!Schema::hasColumn('quotation_items', 'have_sizes')) {
                $table->boolean('have_sizes')->default(false)->after('product_id');
            }
            if (!Schema::hasColumn('quotation_items', 'total_discount') && Schema::hasColumn('quotation_items', 'discount_amount')) {
                // We will use DB statement for rename to be safer with MariaDB versions
                DB::statement("ALTER TABLE `quotation_items` CHANGE `discount_amount` `total_discount` DECIMAL(20,4) DEFAULT 0");
            } elseif (!Schema::hasColumn('quotation_items', 'total_discount')) {
                $table->decimal('total_discount', 20, 4)->default(0)->after('number_discount');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quotation_items', function (Blueprint $table) {
            if (Schema::hasColumn('quotation_items', 'total_discount')) {
                DB::statement("ALTER TABLE `quotation_items` CHANGE `total_discount` `discount_amount` DECIMAL(20,4) DEFAULT 0");
            }
            $table->dropColumn(['type_discount', 'number_discount', 'have_sizes']);
        });
    }
};
