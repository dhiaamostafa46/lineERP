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
            $table->unsignedTinyInteger('transfer_type')->default(1)->comment('1=direct, 2=indirect')->after('to_store_id');
            $table->foreignId('transit_journal_entry_id')->nullable()->after('journal_entry_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('st_direct_transfers', function (Blueprint $table) {
            $table->dropColumn(['transfer_type', 'transit_journal_entry_id']);
        });
    }
};
