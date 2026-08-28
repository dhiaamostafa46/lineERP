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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('phone')->nullable()->unique();
            $table->string('email_verified_at')->nullable();
            $table->string('password');
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->unsignedBigInteger('org_id')->nullable();
            $table->string('photo')->nullable();
            $table->string(column: 'emp_flage')->default(1)->comment('1 = user, 2 = emp');
            $table->string('job_number')->nullable();
            $table->unsignedTinyInteger('status')->default(2)->comment('1 = inactive, 2 = active');
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
