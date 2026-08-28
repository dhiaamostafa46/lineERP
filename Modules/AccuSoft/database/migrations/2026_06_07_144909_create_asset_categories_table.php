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
        Schema::create('asset_categories', function (Blueprint $table) {
            $table->id();
         
            $table->foreignId('branch_id')->nullable()->constrained('branches')->onDelete('cascade');
            $table->boolean('has_accounting_effect')->default(true);
            $table->foreignId('asset_account_id')->nullable()->constrained('tree_accounts')->onDelete('restrict');
            $table->foreignId('accumulated_depreciation_account_id')->nullable()->constrained('tree_accounts')->onDelete('restrict');
            $table->foreignId('depreciation_expense_account_id')->nullable()->constrained('tree_accounts')->onDelete('restrict');
            $table->string('default_depreciation_method')->default('straight_line');
            $table->integer('default_useful_life')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });


         Schema::create('asset_categories_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained('asset_categories')->cascadeOnDelete();
            $table->string('locale', 5)->index();
            $table->string('name');
            $table->text('description')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['asset_id', 'locale']);
            $table->index(['locale', 'name']);
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_categories');
    }
};
