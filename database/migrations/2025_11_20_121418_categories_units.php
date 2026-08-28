<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('org_id')->nullable();
            $table->foreignId('user_id')->nullable()->constrained();
            $table->foreignId('parent_id')->nullable()->constrained('categories')->onDelete('cascade');
            $table->tinyInteger('status')->default(1);
            $table->tinyInteger('type')->default(1);
            $table->string('img')->nullable();
            $table->unsignedInteger('sort')->default(0);
            $table->boolean('is_virtual')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('category_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            $table->string('locale')->index();
            $table->string('name');
            $table->text('description')->nullable();
            $table->unique(['category_id', 'locale']);
            $table->timestamps();
        });

        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained();
            $table->unsignedBigInteger('org_id')->nullable();
            $table->string('code')->nullable();
            $table->decimal('conversion_factor', 15, 5)->default(1);
            $table->boolean('is_base')->default(false);
            $table->tinyInteger('status')->default(1);
            $table->boolean('is_virtual')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('unit_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unit_id')->constrained('units')->onDelete('cascade');
            $table->string('locale')->index();
            $table->string('name');
            $table->string('symbol')->nullable();
            $table->unique(['unit_id', 'locale']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('unit_translations');
        Schema::dropIfExists('units');
        Schema::dropIfExists('category_translations');
        Schema::dropIfExists('categories');
    }
};
