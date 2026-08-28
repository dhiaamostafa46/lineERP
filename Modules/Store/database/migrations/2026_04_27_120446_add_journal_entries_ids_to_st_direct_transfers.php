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
            $table->json('journal_entries_ids')->nullable()->after('transit_journal_entry_id');
        });
    }

    public function down(): void
    {
        Schema::table('st_direct_transfers', function (Blueprint $table) {
            $table->dropColumn('journal_entries_ids');
        });
    }
};
