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
            $table->unsignedBigInteger('expense_account_id')->nullable()->after('main_safe_account_id');
            $table->boolean('enable_cash_movements')->default(true)->after('auto_journal_entry');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pos_devices', function (Blueprint $table) {
            $table->dropColumn(['expense_account_id', 'enable_cash_movements']);
        });
    }
};
