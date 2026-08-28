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
        Schema::create('hr_shift_types', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('status');
            $table->integer('type');
            $table->integer('work_hours');
            $table->text('work_days')->nullable();
            $table->integer('early_entry')->default(0); // الدخول المبكر
            $table->integer('late_entry')->default(0); // الدخول المتأخر
            $table->integer('early_exit')->default(0); // الخروج المبكر
            $table->integer('late_exit')->default(0); // الخروج المتأخر
            $table->integer('entry_end')->default(0); // بداية فترة الدخول
            $table->integer('exit_start')->default(0);// نهاية فترة الخروج
            $table->text('start_date')->nullable();
            $table->text('end_date')->nullable();
            $table->text('exempt_days')->nullable(); // الأيام المعفى منها من الحضور
            $table->timestamps();
            $table->softDeletes();
        });



        Schema::create('hr_shift_type_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hr_shift_type_id')->constrained();
            $table->string('name');
            $table->string('locale')->index();
            $table->unique(['hr_shift_type_id', 'locale']);
            $table->timestamps();
        });

        Schema::create('hr_shifts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('type_id')->constrained('hr_shift_types')->onDelete('cascade');
            $table->time('from');
            $table->time('to');
            $table->boolean('is_active')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hr_shifts');
        Schema::dropIfExists('hr_shift_type_translations');
        Schema::dropIfExists('hr_shift_types');
    }
};
