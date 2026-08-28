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
        Schema::table('pos_devices', function (Blueprint $table) {
            if (!Schema::hasColumn('pos_devices', 'send_to_zatca_phase2')) {
                $table->boolean('send_to_zatca_phase2')->default(true)->after('auto_journal_entry');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pos_devices', function (Blueprint $table) {
            if (Schema::hasColumn('pos_devices', 'send_to_zatca_phase2')) {
                $table->dropColumn('send_to_zatca_phase2');
            }
        });
    }
};
