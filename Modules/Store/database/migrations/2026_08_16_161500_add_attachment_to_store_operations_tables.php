<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * List of store operation tables to add attachment to.
     */
    protected array $tables = [
        'st_receivings',
        'st_issuings',
        'st_settlements',
        'st_damageds',
        'st_opening_balances',
        'st_reservations',
        'st_direct_transfers',
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        foreach ($this->tables as $table) {
            if (Schema::hasTable($table) && !Schema::hasColumn($table, 'attachment')) {
                Schema::table($table, function (Blueprint $table) {
                    $table->string('attachment')->nullable()->after('status');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        foreach ($this->tables as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'attachment')) {
                Schema::table($table, function (Blueprint $table) {
                    $table->dropColumn('attachment');
                });
            }
        }
    }
};
