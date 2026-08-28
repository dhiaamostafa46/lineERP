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
        Schema::table('hr_justifications', function (Blueprint $table) {
            $table->time('to_time')->nullable()->change();
            $table->time('from_time')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hr_justifications', function (Blueprint $table) {
            $table->time('to_time')->nullable(false)->change();
            $table->time('from_time')->nullable(false)->change();
        });
    }
};
