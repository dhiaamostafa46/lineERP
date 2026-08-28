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
        if (!Schema::hasTable('hr_holiday_balances')) {
            Schema::create('hr_holiday_balances', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('employee_id');
                $table->foreignId('type_id')->constrained('hr_holiday_types');
                $table->decimal('balance', 8, 2)->default(0); // الرصيد الحالي
                $table->decimal('annual_balance', 8, 2)->default(0);
                $table->decimal('allowed', 8, 2)->default(0); // الرصيد السنوي
                $table->decimal('previous_balance', 8, 2)->default(0);
                $table->unsignedTinyInteger('status')->default(1); // الرصيد السابق
                $table->timestamps();

                $table->softDeletes();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hr_holiday_balances');
    }
};
