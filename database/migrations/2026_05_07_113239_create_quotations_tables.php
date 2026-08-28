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
        Schema::create('quotations', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('quotation_number')->unique();
            $table->date('issue_date');
            $table->date('expiry_date')->nullable();
            
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('branch_id')->default(1);
            $table->unsignedBigInteger('store_id')->nullable();
            $table->unsignedBigInteger('fiscal_year_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();

            $table->decimal('total_exclusive_vat', 20, 4)->default(0);
            $table->decimal('total_discount', 20, 4)->default(0);
            $table->integer('type_discount')->default(1); // 1: Amount, 2: Percentage
            $table->decimal('number_discount', 20, 4)->default(0);
            $table->decimal('total_vat', 20, 4)->default(0);
            $table->decimal('total_inclusive_vat', 20, 4)->default(0);
            
            $table->decimal('shipping_cost', 20, 4)->default(0);
            $table->decimal('shipping_vat_rate', 5, 2)->default(0);
            $table->decimal('shipping_vat_amount', 20, 4)->default(0);

            $table->string('status')->default('new'); // new, sent, accepted, rejected, expired, converted
            $table->text('notes')->nullable();
            $table->string('file')->nullable();
            
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('customer_id')->references('id')->on('inv_customers')->onDelete('cascade');
        });

        Schema::create('quotation_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('quotation_id');
            $table->unsignedBigInteger('product_id');
            $table->string('product_name');
            $table->string('unit')->nullable();
            $table->unsignedBigInteger('unit_id')->nullable();
            $table->decimal('quantity', 20, 4);
            $table->decimal('unit_price', 20, 4);
            $table->decimal('discount_amount', 20, 4)->default(0);
            $table->decimal('vat_rate', 5, 2)->default(0);
            $table->decimal('vat_amount', 20, 4)->default(0);
            $table->decimal('subtotal_with_vat', 20, 4);
            $table->text('notes')->nullable();
            
            $table->timestamps();
            $table->foreign('quotation_id')->references('id')->on('quotations')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotation_items');
        Schema::dropIfExists('quotations');
    }
};
