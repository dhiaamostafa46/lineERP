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
        // Schema::create('hr_contracts', function (Blueprint $table) {
        //     $table->id();
        //     $table->foreignId('employee_id')->constrained('hr_employees')->onDelete('cascade');
        //     $table->foreignId('type_id')->constrained('hr_contract_types')->onDelete('cascade');
        //     $table->string('file');
        //     $table->string('qiwa_no')->nullable();
        //     $table->date('start_at')->nullable();
        //     $table->date('end_at')->nullable();

        //     $table->integer('signatory')->nullable();
        //     $table->text('data')->nullable();
        //     $table->text('location')->nullable();
        //     $table->text('office')->nullable();
        //     $table->integer('accept')->default(1);

        //     $table->unsignedTinyInteger('status')->default(1)->comment('1 = inactive, 2 = active');
        //     $table->timestamps();
        //     $table->softDeletes();
        // });

        Schema::create('hr_contracts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('hr_employees')->onDelete('cascade');
            $table->foreignId('type_id')->constrained('hr_contract_types')->onDelete('cascade');
            $table->string('contract_number')->nullable();
            $table->string('file')->nullable();
            $table->unsignedTinyInteger('qiwa')->default(0);

            // تواريخ العقد
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->date('signed_date')->nullable();
            $table->integer('duration_months')->nullable();
            $table->boolean('auto_renewable')->default(false);

            // معلومات التوقيع
            $table->text('signatory_company_id')->nullable();
            $table->foreignId('signatory_employee_id')->nullable()->constrained('hr_employees')->onDelete('set null');
            $table->text('company_signature')->nullable();
            $table->text('employee_signature')->nullable();

            // بيانات إضافية
            $table->json('additional_data')->nullable();
            $table->text('location')->nullable();
            $table->text('office')->nullable();
            $table->text('data_conatact')->nullable();
            $table->text('termination_terms')->nullable();

            // حالة العقد
            $table->boolean('accepted_by_employee')->default(false);
            $table->date('accepted_date')->nullable();
            $table->boolean('approved_by_hr')->default(false);
            $table->date('approved_date')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->unsignedTinyInteger('status')->default(1)->comment('1=draft, 2=active, 3=expired, 4=terminated, 5=renewed');

            $table->text('notes')->nullable();
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
        Schema::drop('hr_contracts');
    }
};
