<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hr_settings', function (Blueprint $table) {
            $table->id();
            $table->decimal('min_salary')->nullable();
            $table->unsignedBigInteger('max_off_days')->nullable();
            $table->string('currency')->nullable();
            $table->unsignedInteger('delivery_payroll_at')->nullable();
            $table->unsignedInteger('preparing_payroll_at')->nullable();
            $table->foreignId('payroll_id')->nullable()->constrained('hr_payrolls')->onDelete('cascade');
            $table->date('due_payroll_at')->nullable();
            $table->boolean('payroll_updated')->default(0);
            $table->text('approval_payroll')->nullable();
            $table->boolean('preparing_payroll')->default(0);
            $table->date('next_payroll_date')->nullable();
            $table->date('last_payroll_date')->nullable();
            $table->string('payroll_status')->default('open');
            $table->string('tab')->default('iqama_expiry');

            $table->boolean('calculate_missing_fingerprint')->default(true)->comment('احتساب البصمة الناقصة');
            $table->unsignedTinyInteger('missing_fingerprint_policy')->default(1)->comment('1=نصف يوم, 2=يوم كامل, 3=تجاهل');
            $table->boolean('leave_include_weekend')->default(false)->comment('هل الإجازة تشمل أيام العطلة الأسبوعية');
            $table->boolean('leave_include_holidays')->default(false)->comment('هل الإجازة تشمل الإجازات الرسمية');

            
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('hr_setting_payrolls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('setting_id')->constrained('hr_settings')->onDelete('cascade');
            $table->unsignedInteger('delivery_payroll_at')->nullable();
            $table->unsignedInteger('preparing_payroll_at')->nullable();
            $table->foreignId('payroll_id')->nullable()->constrained('hr_payrolls')->onDelete('cascade');
            $table->date('due_payroll_at')->nullable();
            $table->boolean('payroll_updated')->default(0);
            $table->text('approval_payroll')->nullable();
            $table->boolean('preparing_payroll')->default(0);
            $table->date('next_payroll_date')->nullable();
            $table->date('last_payroll_date')->nullable();
            $table->string('payroll_status')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('hr_setting_payrolls');
        Schema::drop('hr_settings');
    }
};
