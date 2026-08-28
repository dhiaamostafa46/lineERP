<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        // جدول السلف (hr_advances)
        Schema::create('hr_advances', function (Blueprint $table) {
            $table->id();

            // علاقات
            $table->foreignId('employee_id')->constrained('hr_employees')->onDelete('cascade');
            $table->foreignId('approver_id')->nullable()->constrained('users');
            $table->foreignId('payroll_id')->nullable()->constrained('hr_payrolls')->onDelete('cascade');

            // الحقول الجديدة
            $table->date('from_date')->nullable()->comment('تاريخ بداية السلفة');
            $table->date('to_date')->nullable()->comment('تاريخ نهاية السلفة');
            $table->string('attachment')->nullable()->comment('مسار المرفق');
            $table->text('reason')->nullable()->comment('سبب السلفة');

            // الحقول الأساسية
            $table->string('description')->nullable();
            $table->date('due_at')->nullable();
            $table->decimal('amount', 10, 2)->default(0.00);
            $table->unsignedTinyInteger('status')->default(1)->comment('1 = Pending, 2 = Approved, 3 = Rejected');

            $table->timestamps();
            $table->softDeletes();
        });

        // جدول الدفعات الشهرية (hr_monthly_payments)
        Schema::create('hr_monthly_payments', function (Blueprint $table) {
            $table->id();

            // العلاقات
            $table->foreignId('hr_advance_id')->constrained('hr_advances')->onDelete('cascade');
            $table->foreignId('employee_id')->constrained('hr_employees')->onDelete('cascade');
            $table->foreignId('approver_id')->nullable()->constrained('users');
            $table->foreignId('payroll_id')->nullable()->constrained('hr_payrolls')->onDelete('cascade');
            $table->string('attachment')->nullable()->comment('مسار المرفق');
            // الحقول

            $table->date('due_at')->comment('تاريخ الاستحقاق');
            $table->decimal('amount', 10, 2)->default(0.00);
            $table->unsignedTinyInteger('status')->default(1)->comment('1 = Pending, 2 = Approved, 3 = Rejected');
            $table->unsignedTinyInteger('type')->default(1)->comment('1 = Pending,   2 = Approved, 3 = Rejected');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('hr_monthly_payments');
        Schema::dropIfExists('hr_advances');
    }
};
