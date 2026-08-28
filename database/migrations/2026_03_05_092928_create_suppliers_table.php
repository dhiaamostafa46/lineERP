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
        Schema::create('inv_suppliers', function (Blueprint $table) {
            $table->id();
            // البيانات الأساسية للمورد
            $table->string('phone')->nullable();
            $table->string('email')->nullable();

            // البيانات الضريبية والهوية (مطلوبة لمتطلبات الفواتير الضريبية للمشتريات)
            $table->string('vat_number')->nullable()->comment('الرقم الضريبي');
            $table->string('cr_number')->nullable()->comment('رقم السجل التجاري');
            $table->text('address')->nullable();


              $table->string('country', 5)->default('SA');
            $table->string('city')->nullable();
            $table->string('district')->nullable();
            $table->string('street')->nullable();

            $table->string('building_number')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('additional_number')->nullable();


            // الربط المحاسبي بشجرة الحسابات
            // يربط المورد بحسابه المالي في دليل الحسابات (TreeAccounts) تحت حساب الموردين الرئيسي
            $table->foreignId('tree_account_id')->nullable()->constrained('tree_accounts')->nullOnDelete();

            // الربط الإداري والمالي

            $table->decimal('credit_limit', 15, 4)->default(0)->comment('سقف المديونية مع المورد');
            $table->boolean('status')->default(true);
                $table->string('file')->nullable();
                 $table->foreignId('user_id')->nullable()->constrained('users');

            $table->timestamps();
            $table->softDeletes();
        });



        // جدول ترجمة الموردين لدعم تعدد اللغات
        Schema::create('inv_supplier_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inv_supplier_id')->constrained('inv_suppliers')->onDelete('cascade');
            $table->string('locale')->index();
            $table->string('name');
            $table->unique(['inv_supplier_id', 'locale']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inv_supplier_translations');
        Schema::dropIfExists('inv_suppliers');
    }
};
