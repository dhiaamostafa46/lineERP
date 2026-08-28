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
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();

            $table->string('phone')->nullable();
            $table->string('email')->nullable();

            $table->string('contact_person')->nullable();

            $table->text('address')->nullable();

            $table->foreignId('city_id')->nullable()->constrained('cities')->nullOnDelete();

            $table->unsignedTinyInteger('status')->default(2)->comment('1=inactive,2=active');

            $table->timestamps();
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->foreignId('deleted_by')->nullable()->constrained('users');
            $table->softDeletes();

            // Indexes
            $table->index('code');
            $table->index('status');
        });
        Schema::create('company_translations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('company_id')
                ->constrained('companies')
                ->cascadeOnDelete();

            $table->string('locale')->index();

            $table->string('name');
            $table->unique(['company_id', 'locale']);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_translations');
        Schema::dropIfExists('companies');
    }
};
