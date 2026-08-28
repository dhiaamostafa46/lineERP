<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('hr_attendance_policies')) {
            Schema::create('hr_attendance_policies', function (Blueprint $table) {
                $table->id();

                $table->text('description')->nullable();
                $table->boolean('is_automatic')->default(false);
                $table->unsignedTinyInteger('scope')->default(1)->comment('1=موظف, 2=قسم, 3=وظيفة, 4=فرع');
                $table->json('scope_ids')->nullable()->comment('Contains IDs of employees, departments, positions, or branches depending on scope');
                $table->date('start_date')->nullable();
                $table->date('end_date')->nullable();
                $table->unsignedTinyInteger('status')->default(2)->comment('Status: 1=Inactive, 2=Active.');
                $table->unsignedTinyInteger('type')->comment('Type: 1=Absence, 2=Late, 3=Early Exit, 4=Overtime.');
                $table->unsignedTinyInteger('calculation_type')->comment('1=Per Day, 2=Per Hour');
                $table->json('settings')->nullable();
                $table->json('salary_effect')->nullable()->comment('Defines how this policy affects salary (deduction or addition)');
                $table->timestamps();
                $table->softDeletes();
                $table->index(['status', 'type']);
            });
        }

        if (!Schema::hasTable('hr_attendance_policy_translations')) {
            Schema::create('hr_attendance_policy_translations', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('hr_attendance_policy_id'); // Changed from 'policy_id'
                $table->string('name', 255);
                $table->string('locale', 10)->index()->comment('Language code (en, ar)');
                $table->unique(['hr_attendance_policy_id', 'locale'], 'policy_locale_unique');

                $table->foreign('hr_attendance_policy_id', 'fk_policy_trans')->references('id')->on('hr_attendance_policies')->onDelete('cascade');

                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('hr_attendance_policy_translations');
        Schema::dropIfExists('hr_attendance_policies');
    }
};
