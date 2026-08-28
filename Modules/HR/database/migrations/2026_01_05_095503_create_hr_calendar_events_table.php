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
        if (!Schema::hasTable('hr_calendar_events')) {
            Schema::create('hr_calendar_events', function (Blueprint $table) {
                $table->id();
                $table->date('start_date')->nullable()->index();
                $table->date('end_date')->nullable()->index();
                $table->text('description')->nullable();
                $table->json('rules')->nullable();
                $table->boolean('is_recurring')->default(false);
                $table->unsignedTinyInteger('status')->default(1);
                $table->unsignedTinyInteger('type')->default(1)->comment('1=Holiday, 2=Event');
                $table->string('color')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (!Schema::hasTable('hr_calendar_event_translations')) {
            Schema::create('hr_calendar_event_translations', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('hr_calendar_event_id'); // Changed from 'policy_id'
                $table->string('name', 255);
                $table->string('locale', 10)->index()->comment('Language code (en, ar)');
                $table->unique(['hr_calendar_event_id', 'locale'], 'hr_calendar_event_locale_unique');
                $table->foreign('hr_calendar_event_id', 'fk_hr_calendar_event_trans')->references('id')->on('hr_calendar_events')->onDelete('cascade');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hr_calendar_event_translations');
        Schema::dropIfExists('hr_calendar_events');
    }
};
