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
        Schema::create('cost_centers', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // CC-001

            // البنية الشجرية (مراكز رئيسية وفرعية)
            $table->foreignId('parent_id')->nullable()
                  ->constrained('cost_centers')
                  ->nullOnDelete();

            $table->integer('level')->default(1);
            $table->boolean('is_leaf')->default(true); // يقبل قيود

            $table->boolean('status')->default(true);
            $table->json('attributes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // فهارس
            $table->index('code');
            $table->index('parent_id');
            $table->index('status');
        });

        // ⭐ التغيير: استخدام cost_centers_id بدلاً من cost_center_id
        Schema::create('cost_center_translations', function (Blueprint $table) {
            $table->id();
            // ⭐ تغيير من cost_center_id إلى cost_centers_id
            $table->foreignId('cost_centers_id')->constrained('cost_centers')->onDelete('cascade');
            $table->string('locale')->index();
            $table->string('name');
            $table->text('description')->nullable();
            // ⭐ تحديث الـ unique constraint
            $table->unique(['cost_centers_id', 'locale']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cost_center_translations');
        Schema::dropIfExists('cost_centers');
    }
};
