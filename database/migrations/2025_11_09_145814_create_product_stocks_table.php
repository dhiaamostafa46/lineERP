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
        // جدول لتخزين الكمية الحالية للمنتج في كل مستودع
        // Schema::create('product_stocks', function (Blueprint $table) {
        //     $table->id();
        //     $table->unsignedBigInteger('org_id');
        //     $table->unsignedBigInteger('branch_id');
        //     $table->unsignedBigInteger('depot_id');
        //     $table->unsignedBigInteger('product_unit_id');
        //     $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
        //     $table->decimal('quantity', 15, 4)->default(0);
        //     $table->timestamps();
        //     // لضمان عدم تكرار المنتج في نفس المستودع
        //     $table->unique(['product_id', 'depot_id', 'branch_id', 'org_id'], 'product_stock_unique');
        // });

        // // جدول لتسجيل حركات المخزون (إدخال، إخراج، تحويل)
        // Schema::create('stock_movements', function (Blueprint $table) {
        //     $table->id();
        //     $table->unsignedBigInteger('org_id');
        //     $table->unsignedBigInteger('branch_id');
        //     $table->unsignedBigInteger('depot_id');
        //     $table->foreignId('user_id')->nullable()->constrained('users');
        //     $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
        //     $table->foreignId('unit_id')->constrained('units');
        //     $table->tinyInteger('type')->default(1); // نوع الحركة
        //     $table->decimal('quantity', 15, 4); // الكمية بالوحدة المحددة
        //     $table->decimal('quantity_in_base_unit', 15, 4); // الكمية المحولة للوحدة الأساسية
        //     $table->decimal('cost_price', 12, 4)->nullable(); // سعر التكلفة للوحدة وقت الحركة
        //     $table->string('reference_type')->nullable(); // e.g., 'PurchaseInvoice', 'SaleInvoice'
        //     $table->unsignedBigInteger('reference_id')->nullable();
        //     $table->text('notes')->nullable();
        //     $table->timestamps();
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Schema::dropIfExists('stock_movements');
        // Schema::dropIfExists('product_stocks');
    }
};
