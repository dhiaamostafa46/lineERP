<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('st_direct_transfers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('org_id');
            $table->unsignedBigInteger('branch_id');
            $table->foreignId('user_id')->constrained();
            $table->string('document_number')->unique();
            $table->date('document_date');
            $table->foreignId('from_store_id')->constrained('stores')->onDelete('cascade');
            $table->foreignId('to_store_id')->constrained('stores')->onDelete('cascade');
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

        Schema::create('st_direct_transfer_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('direct_transfer_id')->constrained('st_direct_transfers')->onDelete('cascade');
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
        Schema::dropIfExists('st_direct_transfer_items');
        Schema::dropIfExists('st_direct_transfers');
    }
};
