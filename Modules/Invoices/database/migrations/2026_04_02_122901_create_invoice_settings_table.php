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
        Schema::create('invoice_settings', function (Blueprint $table) {
            $table->id();

            // --- إعدادات المبيعات (Sales) ---
            $table->string('sales_prefix')->default('INV'); // بادئة الفاتورة
            $table->unsignedInteger('sales_next_number')->default(1); // الرقم التالي
            $table->boolean('sales_auto_post')->default(true); // ترحيل تلقائي للمحاسبة
            $table->text('sales_terms')->nullable(); // الشروط والأحكام في الفاتورة




            // --- إعدادات المشتريات (Purchases) ---
            $table->string('purchase_prefix')->default('PUR');
            $table->unsignedInteger('purchase_next_number')->default(1);
            $table->text('purchase_terms')->nullable();

            // --- إعدادات أوامر الشراء (Purchase Orders) ---
            $table->string('purchase_order_prefix')->default('PO');
            $table->unsignedInteger('purchase_order_next_number')->default(1);




            // --- إعدادات المرتجعات (Returns) ---
            $table->string('sales_return_prefix')->default('SRET');
            $table->string('purchase_return_prefix')->default('PRET');
            $table->unsignedInteger('sales_return_next_number')->default(1);
            $table->unsignedInteger('purchase_return_next_number')->default(1);





            // --- إعدادات عروض الأسعار (Quotations) ---
            $table->string('quotation_prefix')->default('QUO');
            $table->integer('quotation_validity_days')->default(15); // صلاحية العرض بالأيام
            $table->text('quotation_terms')->nullable();
             $table->unsignedInteger('quotation_next_number')->default(1);




            // --- إعدادات الضريبة والزكاة (VAT & ZATCA) ---
            $table->decimal('default_vat_rate', 5, 2)->default(15.00); // نسبة الضريبة الافتراضية
            $table->boolean('prices_include_vat')->default(false);
            $table->boolean('allow_negative_stock')->default(false);

            // هل الأسعار تشمل الضريبة؟



            // --- خيارات الطباعة والعرض (UI & Print) ---
            $table->boolean('show_logo_in_print')->default(true);
            $table->boolean('show_customer_balance')->default(false);
            $table->string('invoice_footer_text')->nullable();


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_settings');
    }
};

