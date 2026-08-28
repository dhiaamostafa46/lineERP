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
        Schema::create('st_reservations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('org_id')->nullable();
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->string('document_number')->unique();
            $table->date('document_date');
            $table->unsignedBigInteger('store_id');
            $table->tinyInteger('status')->default(1); // 1: Draft, 2: Approved (Reserved), 3: Returned, 4: Cancelled
            $table->integer('total_items')->default(0);
            $table->decimal('total_quantity', 15, 4)->default(0);
            $table->decimal('total_value', 15, 4)->default(0);
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->unsignedBigInteger('returned_by')->nullable();
            $table->timestamp('returned_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('st_reservation_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('reservation_id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('unit_id');
            $table->boolean('have_sizes')->default(false);
            $table->json('unit')->nullable();
            $table->decimal('quantity', 15, 4);
            $table->decimal('unit_cost', 15, 4);
            $table->decimal('total_cost', 15, 4);
            $table->tinyInteger('status')->default(1);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('reservation_id')->references('id')->on('st_reservations')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('st_reservation_items');
        Schema::dropIfExists('st_reservations');
    }
};
