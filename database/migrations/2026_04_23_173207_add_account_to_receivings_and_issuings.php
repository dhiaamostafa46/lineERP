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
        Schema::table('st_receivings', function (Blueprint $table) {
            $table->unsignedBigInteger('tree_account_id')->nullable()->after('store_id');
        });

        Schema::table('st_issuings', function (Blueprint $table) {
            $table->unsignedBigInteger('tree_account_id')->nullable()->after('store_id');
        });
    }

    public function down(): void
    {
        Schema::table('st_receivings', function (Blueprint $table) {
            $table->dropColumn('tree_account_id');
        });

        Schema::table('st_issuings', function (Blueprint $table) {
            $table->dropColumn('tree_account_id');
        });
    }
};
