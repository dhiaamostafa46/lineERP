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
        Schema::create('pos_sessions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('device_id');
            $table->unsignedBigInteger('user_id'); // Cashier
            
            $table->decimal('opening_balance', 15, 4)->default(0);
            $table->timestamp('opened_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            
            $table->decimal('expected_cash', 15, 4)->default(0);
            $table->decimal('actual_cash', 15, 4)->default(0);
            $table->decimal('difference', 15, 4)->default(0);
            
            $table->enum('status', ['open', 'closed'])->default('open');
            $table->unsignedBigInteger('closing_journal_entry_id')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pos_sessions');
    }
};
