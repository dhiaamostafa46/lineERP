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
        Schema::create('zatca_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->unique()->constrained('branches')->cascadeOnDelete();

            // بيانات الهوية التقنية
            $table->string('uuid')->comment('EGS Serial Number / UUID');
            $table->string('common_name')->nullable()->comment('اسم المنشأة أو الجهاز');
            $table->enum('environment', ['sandbox', 'simulation', 'production'])->default('sandbox');

            // المفاتيح والشهادات (مطلوبة للتوقيع الرقمي)
            $table->text('private_key')->nullable();
            $table->text('csr')->nullable(); // Certificate Signing Request
            $table->text('binary_security_token')->nullable(); // الشهادة المستلمة
            $table->text('secret')->nullable(); // الرقم السري الخاص بالشهادة

            $table->dateTime('expiry_date')->nullable();
             $table->foreignId('user_id')->nullable()->constrained('users');
            $table->boolean('is_active')->default(false);

            $table->json('business_category')->nullable()->comment('نوع النشاط التجاري حسب تصنيف الهيئة');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('zatca_settings');
    }
};
