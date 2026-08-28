<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('accounting_settings', function (Blueprint $table) {
            $table->id();
            $table->string('currency', 3)->default('SAR');
            $table->unsignedTinyInteger('decimal_places')->default(2);
            $table->string('journal_prefix')->default('JE');
            $table->unsignedInteger('journal_next_number')->default(1);
            $table->boolean('allow_backdated_entries')->default(false);
            $table->boolean('allow_future_dated_entries')->default(false);
            $table->boolean('lock_period_pwd_enabled')->default(false);
            $table->string('lock_period_pwd')->nullable();
            $table->timestamps(); // ✅ مرة واحدة فقط
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounting_settings');
    }
};
