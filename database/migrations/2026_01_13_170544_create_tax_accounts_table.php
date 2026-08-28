<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tax_accounts', function (Blueprint $table) {
            $table->id();
            $table->decimal('rate', 5, 2)->default(0)->comment('معدل الضريبة (%)');
            $table->unsignedTinyInteger('status')->default(2)->comment('1=inactive, 2=active');
            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
        });

        Schema::create('tax_account_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tax_account_id')->constrained('tax_accounts')->cascadeOnDelete();
            $table->string('locale', 5)->index();
            $table->string('name');

            $table->unique(['tax_account_id', 'locale']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tax_account_translations');
        Schema::dropIfExists('tax_accounts');
    }
};
