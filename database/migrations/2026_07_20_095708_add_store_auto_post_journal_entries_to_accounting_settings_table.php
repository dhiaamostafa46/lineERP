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
        Schema::table('accounting_settings', function (Blueprint $table) {
            $table->boolean('store_auto_post_journal_entries')->default(false)->after('driver_auto_post_journal_entries');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('accounting_settings', function (Blueprint $table) {
            $table->dropColumn('store_auto_post_journal_entries');
        });
    }
};
