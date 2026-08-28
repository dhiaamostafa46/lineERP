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
        Schema::table('st_direct_transfers', function (Blueprint $table) {
            if (Schema::hasColumn('st_direct_transfers', 'transit_journal_entry_id')) {
                $table->dropColumn('transit_journal_entry_id');
            }
            $table->decimal('returned_quantity', 15, 4)->default(0)->after('total_quantity');
            $table->tinyInteger('return_status')->default(0)->after('status')->comment('0: None, 1: Partial, 3: Full');
        });

        Schema::table('st_direct_transfer_items', function (Blueprint $table) {
            $table->decimal('returned_quantity', 15, 4)->default(0)->after('received_quantity');
        });
    }

    public function down(): void
    {
        Schema::table('st_direct_transfers', function (Blueprint $table) {
            $table->dropColumn(['returned_quantity', 'return_status']);
            $table->unsignedBigInteger('transit_journal_entry_id')->nullable()->after('journal_entry_id');
        });

        Schema::table('st_direct_transfer_items', function (Blueprint $table) {
            $table->dropColumn('returned_quantity');
        });
    }
};
