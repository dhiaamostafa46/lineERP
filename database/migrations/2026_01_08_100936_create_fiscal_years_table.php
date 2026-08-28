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
        Schema::create('fiscal_years', function (Blueprint $table) {
            $table->id();

            $table->date('start_date'); // تاريخ البداية
            $table->date('end_date'); // تاريخ النهاية
            $table->boolean('is_current')->default(false); // السنة الحالية
            $table->boolean('is_closed')->default(false); // مقفلة
            $table->text('notes')->nullable();

            $table->timestamp('closed_at')->nullable();
            $table->foreignId('closed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->json('closed_periods')->nullable();
            $table->text('closure_note')->nullable();
            $table->json('pre_closing_balances')->nullable();
            $table->json('post_closing_balances')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // فهرس للبحث السريع
            $table->index('is_current');
            $table->index('is_closed');
        });

        // Schema::create('closure_logs', function (Blueprint $table) {
        //     $table->id();
        //     $table->unsignedBigInteger('fiscal_year_id');
        //     $table->unsignedBigInteger('closed_by');
        //     $table->timestamp('closed_at');
        //     $table->json('events')->nullable();
        //     $table->timestamps();

        //     $table->foreign('fiscal_year_id')->references('id')->on('fiscal_years');
        //     $table->foreign('closed_by')->references('id')->on('users');
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //  Schema::dropIfExists('closure_logs');
        Schema::dropIfExists('fiscal_years');
    }
};
