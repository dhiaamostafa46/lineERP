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
        Schema::create('pos_session_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pos_session_id');
            $table->decimal('amount', 15, 4)->default(0);
            $table->enum('type', ['cash_in', 'cash_out']);
            $table->string('reason')->nullable();
            $table->unsignedBigInteger('user_id'); // who made the transaction
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pos_session_transactions');
    }
};
