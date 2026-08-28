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
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->string('chachtoken');
            $table->string('type')->nullable();
            $table->string('price')->nullable();
            $table->string(column: 'from_user')->nullable();
            $table->string(column: 'to_user')->nullable();
            $table->string(column: 'payment_type')->nullable();
            $table->date(column: 'date')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
