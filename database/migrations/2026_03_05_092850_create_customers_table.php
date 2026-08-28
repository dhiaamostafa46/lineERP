<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('inv_customers', function (Blueprint $table) {
            $table->id();
            // 🧾 بيانات الاتصال
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            // 🏢 بيانات ضريبية
            $table->string('vat_number', 50)->nullable()->index();
            $table->string('cr_number', 50)->nullable();
            // 📍 العنوان (داخل نفس الجدول - بشكل منظم)
            $table->string('country', 5)->default('SA');
            $table->string('city')->nullable();
            $table->string('district')->nullable();
            $table->string('street')->nullable();

            $table->string('building_number')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('additional_number')->nullable(); // مهم في السعودية
            // 💰 الحساب المحاسبي
            $table->foreignId('tree_account_id')->nullable()->constrained('tree_accounts')->nullOnDelete();
            $table->decimal('credit_limit', 15, 4)->default(0);
            // ⚙️ حالة
            $table->boolean('status')->default(true);
             $table->foreignId('user_id')->nullable()->constrained('users');
            // 📎 مرفقات
            $table->string('file')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('inv_customer_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inv_customer_id')->constrained('inv_customers')->onDelete('cascade');
            $table->string('locale')->index();
            $table->string('name');
            $table->unique(['inv_customer_id', 'locale']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inv_customer_translations');
        Schema::dropIfExists('inv_customers');
    }
};
