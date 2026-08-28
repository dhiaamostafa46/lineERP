



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
        Schema::create('hr_groups', function (Blueprint $table) {
            $table->id();
            $table->longText('description')->nullable();
            $table->string('member')->nullable();
            $table->unsignedTinyInteger('status')->default(2);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('hr_group_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hr_group_id')->constrained();
            $table->string('name');
            $table->string('locale')->index();
            $table->unique(['hr_group_id', 'locale']);
            $table->timestamps();
        });

        Schema::create('hr_group_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hr_group_id')->constrained('hr_groups')->onDelete('cascade');
            $table->foreignId('employee_id')->nullable()->constrained('hr_employees')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hr_group_details');
        Schema::dropIfExists('hr_group_translations');
        Schema::dropIfExists('hr_groups');
    }
};
