<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Inventory Receiving (الاستلام المخزني)
        Schema::create('st_receivings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('org_id');
            $table->unsignedBigInteger('branch_id');
            $table->foreignId('user_id')->constrained();
            $table->string('document_number')->unique();
            $table->date('document_date');
            $table->foreignId('store_id')->constrained('stores')->onDelete('cascade');
            $table->string('reference_type')->nullable()->comment('purchases, return, etc.');
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->unsignedTinyInteger('status')->default(1)->comment('1=draft,2=approved,4=cancelled');
            $table->integer('total_items')->default(0);
            $table->decimal('total_quantity', 15, 4)->default(0);
            $table->decimal('total_value', 15, 4)->default(0);
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('journal_entry_id')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('st_receiving_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('receiving_id')->constrained('st_receivings')->onDelete('cascade');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('unit_id');
            $table->boolean('have_sizes')->default(false);
            $table->decimal('quantity', 15, 4);
            $table->decimal('unit_cost', 15, 4);
            $table->decimal('total_cost', 15, 4);
            $table->text('unit');
            $table->unsignedTinyInteger('status')->default(1);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 2. Inventory Issuing (الصرف المخزني)
        Schema::create('st_issuings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('org_id');
            $table->unsignedBigInteger('branch_id');
            $table->foreignId('user_id')->constrained();
            $table->string('document_number')->unique();
            $table->date('document_date');
            $table->foreignId('store_id')->constrained('stores')->onDelete('cascade');
            $table->string('reference_type')->nullable()->comment('sales, internal, etc.');
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->unsignedTinyInteger('status')->default(1)->comment('1=draft,2=approved,4=cancelled');
            $table->integer('total_items')->default(0);
            $table->decimal('total_quantity', 15, 4)->default(0);
            $table->decimal('total_value', 15, 4)->default(0);
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('journal_entry_id')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('st_issuing_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('issuing_id')->constrained('st_issuings')->onDelete('cascade');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('unit_id');
            $table->boolean('have_sizes')->default(false);
            $table->decimal('quantity', 15, 4);
            $table->decimal('unit_cost', 15, 4);
            $table->decimal('total_cost', 15, 4);
            $table->text('unit');
            $table->unsignedTinyInteger('status')->default(1);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('st_issuing_items');
        Schema::dropIfExists('st_issuings');
        Schema::dropIfExists('st_receiving_items');
        Schema::dropIfExists('st_receivings');
    }
};
