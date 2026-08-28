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
        if (!Schema::hasTable('hr_justifications')) {
            Schema::create('hr_justifications', function (Blueprint $table) {
                $table->id();

                $table->foreignId('shift_id')->nullable();
                $table->foreignId('employee_id')->constrained('hr_employees')->onDelete('cascade');
                $table->text('reason'); // سبب التأخير
                $table->integer('type')->default(1)->comment('1 = late, 2 = early_leave'); // نوع التسوية
                $table->unsignedTinyInteger('status')->default(1)->comment('1 = pending, 2 = approved, 3 = rejected');
                $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('approved_at')->nullable();
                $table->date('request_date');
                $table->time('to_time');
                $table->time('from_time');

                $table->string('attachment')->nullable();
                $table->foreignId('approver_id')->nullable()->constrained('users');
                $table->softDeletes();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hr_justifications');
    }
};
