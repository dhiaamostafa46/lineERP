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
        Schema::table('pos_session_transactions', function (Blueprint $table) {
            $table->unsignedBigInteger('pos_payment_method_id')->nullable()->after('pos_session_id');
            $table->string('notes')->nullable()->after('reason');
            $table->unsignedBigInteger('reference_id')->nullable()->after('notes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pos_session_transactions', function (Blueprint $table) {
            $table->dropColumn(['pos_payment_method_id', 'notes', 'reference_id']);
        });
    }
};
