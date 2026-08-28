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
        Schema::table('assets', function (Blueprint $table) {
            $table->unsignedBigInteger('tax_amount')->nullable()->after('purchase_value');
            $table->string('tax_type')->nullable()->after('tax_amount');
            $table->unsignedBigInteger('payment_account_id')->nullable()->after('tax_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->dropColumn(['tax_amount', 'tax_type', 'payment_account_id']);
        });
    }
};
