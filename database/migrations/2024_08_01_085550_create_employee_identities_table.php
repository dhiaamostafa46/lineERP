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
        Schema::create('employee_identities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained();
            $table->unsignedTinyInteger('identity_type')->nullable()->comment('1 => identity, 2 =>residence');
            $table->string('identity_no')->nullable();
            $table->string('insurance_no')->nullable();
            $table->date('identity_expired_at')->nullable();
            $table->date('insurance_expired_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_identities');
    }
};
