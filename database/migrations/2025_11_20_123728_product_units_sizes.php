<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->foreignId('unit_id')->constrained('units');
            $table->decimal('conversion_factor', 15, 5)->default(1);
            // الأسعار والتكاليف لهذه الوحدة
            $table->decimal('cost_price', 15, 4)->default(0);
            $table->decimal('prod_price', 15, 4)->default(0);

            $table->string('barcode')->nullable();
            $table->boolean('is_base')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['product_id', 'unit_id']);
            $table->index(['product_id', 'is_base']);
        });



        Schema::create('product_sizes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->decimal('sale_price', 15, 4)->default(0);
            $table->decimal('cost_price', 15, 4)->default(0);
            $table->decimal('consumption_factor', 12, 4)->default(1.0);
            $table->string('barcode')->nullable();
            $table->tinyInteger('sort_order')->default(0);
            $table->tinyInteger('status')->default(1);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('product_size_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_size_id')->constrained('product_sizes')->onDelete('cascade');
            $table->string('locale')->index();
            $table->string('name');
            $table->string('description')->nullable();
            $table->unique(['product_size_id', 'locale']);
            $table->timestamps();
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('product_size_translations');
        Schema::dropIfExists('product_sizes');
        Schema::dropIfExists('product_units');
    }
};
