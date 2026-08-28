<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 1. جدول الأرصدة الحالية (Stock Balance)
        Schema::create('stocks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('org_id');
            $table->unsignedBigInteger('branch_id');
            $table->foreignId('store_id')->constrained('stores')->onDelete('cascade');

            $table->unsignedBigInteger('product_id');

             $table->unsignedBigInteger('unit_id');

            // الكميات
            $table->decimal('current_quantity', 15, 4)->default(0);
            $table->decimal('reserved_quantity', 15, 4)->default(0);
            $table->decimal('available_quantity', 15, 4)->storedAs('current_quantity - reserved_quantity');

            // التكاليف
            $table->decimal('average_cost', 15, 4)->default(0);
            $table->decimal('last_cost', 15, 4)->default(0);
            $table->decimal('total_value', 15, 4)->storedAs('current_quantity * average_cost');

            // حدود المخزون
            $table->decimal('min_quantity', 15, 4)->nullable();
            $table->decimal('reorder_point', 15, 4)->nullable();

            $table->timestamp('last_movement_at')->nullable();
            $table->boolean('is_size')->default(false);

            $table->timestamps();
            $table->softDeletes();

        });

        // 2. جدول حركات المخزون (Stock Movements) - جدول واحد لكل شيء
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('org_id')->nullable();
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->foreignId('user_id')->constrained();
            $table->unsignedBigInteger('product_id');
            // معلومات الحركة الأساسية
            $table->string('movement_number')->unique();
            $table->date('movement_date');
       $table->enum('stock_type', ['in', 'out', 'count', 'adjustment'])
      ->comment('نوع حركة المخزون');

            $table->unsignedTinyInteger('movement_type')->comment('1:opening_balance, 2:purchase, 3:sale, 4:transfer_out, 5:transfer_in, ' . '6:adjustment, 7:damage, 8:production_in, 9:production_out');

            // المخزن والمنتج
            $table->foreignId('store_id')->constrained('stores');
            $table->unsignedBigInteger('unit_id');

            // الكمية والتكلفة
            $table->decimal('quantity', 15, 4); // موجب للوارد، سالب للصادر
            $table->decimal('unit_cost', 15, 4)->default(0);
            $table->decimal('total_cost', 15, 4)->default(0);
            $table->boolean('is_size')->default(false);
            // للتحويلات فقط
            $table->foreignId('to_store_id')->nullable()->constrained('stores');
            $table->unsignedBigInteger('related_movement_id')->nullable()->comment('ربط الحركة المقابلة في التحويل');

            // للتسويات والتالف
            $table->unsignedTinyInteger('reason')->nullable()->comment('1:count_variance, 2:damage, 3:expired, 4:theft, 5:other');

            // الربط مع المستندات الأصلية
            $table->string('reference_type')->nullable()->comment('Invoice, PurchaseOrder, etc');
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->string('reference_number')->nullable();

            // الحالة
            $table->unsignedTinyInteger('status')->default(1)->comment(
                '1:draft, 2:approved, 3:cancelled'
            );
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamp('approved_at')->nullable();

            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // الفهارس
            $table->index(['movement_type', 'movement_date']);
            $table->index(['store_id', 'movement_date']);
            $table->index(['reference_type', 'reference_id']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
        Schema::dropIfExists('stocks');
    }
};
