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
        Schema::create('sales_invoice_zatcas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sales_invoice_id')->constrained('sales_invoices')->cascadeOnDelete();
            // الملفات التقنية للمرحلة الثانية
            $table->longText('xml_content')->nullable()->comment('ملف الفاتورة بصيغة XML');
            $table->longText('request_payload')->nullable(); // البيانات المرسلة للهيئة
            $table->longText('response_payload')->nullable(); // رد الهيئة بالكامل
            $table->string('request_id')->nullable();
            $table->json('validation_results')->nullable()->comment('نتائج التحقق (Warnings/Errors)');
            $table->timestamps();
            $table->index('sales_invoice_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_invoice_zatcas');
    }
};
