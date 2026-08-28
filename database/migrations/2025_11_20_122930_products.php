<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('org_id')->nullable();
            $table->foreignId('user_id')->nullable()->constrained();
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            $table->tinyInteger('type')->default(1);
            $table->foreignId('base_unit_id')->nullable()->constrained('units');

            $table->unsignedBigInteger('kitchen_id')->nullable();
            // الأساسيات
            $table->string('sku')->unique()->nullable();
            $table->string('barcode')->nullable();
            $table->string('img')->nullable();

            $table->time('s_from')->nullable();
            $table->time('s_to')->nullable();
            $table->text('work_days')->nullable();
            // التكاليف والأسعار
            $table->decimal('cost_price', 15, 4)->default(0);
            $table->decimal('prod_price', 15, 4)->default(0);
            $table->decimal('vat', 5, 2)->default(0);

            // إعدادات المنتج
            $table->boolean('track_stock')->default(true);
            $table->boolean('sellable')->default(true);
            $table->boolean('purchasable')->default(true);
            $table->boolean('have_sizes')->default(false);
            $table->boolean('have_variants')->default(false);

            // معلومات إضافية
            $table->decimal('calories', 10, 2)->nullable();
            $table->integer('min_quantity')->default(0);
            $table->integer('reorder_point')->default(0);

            $table->tinyInteger('status')->default(1);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['org_id', 'sku']);
            $table->index(['org_id', 'barcode']);
        });


        Schema::create('product_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->string('locale')->index();
            $table->string('name');
            $table->text('details')->nullable();
            $table->text('specifications')->nullable();
            $table->unique(['product_id', 'locale']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_translations');
        Schema::dropIfExists('products');
    }
};
