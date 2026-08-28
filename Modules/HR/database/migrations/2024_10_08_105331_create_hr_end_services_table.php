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
        Schema::create('hr_end_services', function (Blueprint $table) {
            $table->id();

            $table->foreignId('employee_id')->constrained('hr_employees')->onDelete('cascade');
            $table->date('end')->nullable();

            $table->text('description')->nullable(); // للوصف
            $table->string('reason')->nullable(); // للسبب
            $table->decimal('reward_amount', 10, 2)->nullable(); // لقيمة المكافأة
            $table->boolean('approved')->default(false); // للموافقة
            $table->unsignedTinyInteger('status')->comment('1 => inactive, 2 => active')->default(2);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hr_end_services');
    }
};
