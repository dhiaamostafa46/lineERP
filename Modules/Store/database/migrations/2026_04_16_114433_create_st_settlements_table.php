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
        Schema::create('st_settlements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('org_id')->nullable();
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->foreignId('user_id')->nullable()->constrained();
            $table->string('document_number')->unique();
            $table->date('document_date');
            $table->foreignId('store_id')->constrained('stores');
            $table->unsignedTinyInteger('status')->default(1)->comment('1=Draft, 2=Approved');
            $table->unsignedInteger('total_items')->default(0);
            $table->decimal('total_quantity', 15, 4)->default(0);
            $table->decimal('total_value', 15, 4)->default(0);
            $table->foreignId('journal_entry_id')->nullable()->constrained('journal_entries');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('st_settlement_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('settlement_id')->constrained('st_settlements')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products');
            $table->foreignId('unit_id')->constrained('units');
            $table->json('unit')->nullable(); // JSON configuration of units
            $table->boolean('have_sizes')->default(false);
            $table->decimal('system_quantity', 15, 4)->default(0);
            $table->decimal('actual_quantity', 15, 4)->default(0);
            $table->decimal('variance_quantity', 15, 4)->default(0);
            $table->decimal('unit_cost', 15, 4)->default(0);
            $table->decimal('total_cost', 15, 4)->default(0);
            $table->string('variance_type')->comment('in=Surplus (زيادة), out=Shortage (عجز)');
            $table->unsignedTinyInteger('status')->default(1);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('st_settlement_items');
        Schema::dropIfExists('st_settlements');
    }
};
