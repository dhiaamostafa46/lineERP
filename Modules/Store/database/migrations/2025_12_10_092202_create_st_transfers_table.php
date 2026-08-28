<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // ========================================
        // جدول التحويلات الصادرة
        // ========================================
        Schema::create('st_transfer_outs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('org_id');
            $table->unsignedBigInteger('branch_id');
            $table->foreignId('user_id')->constrained()->comment('المستخدم الذي أنشأ التحويل');

            // رقم وتاريخ المستند
            $table->string('document_number')->unique()->comment('رقم مستند التحويل');
            $table->date('document_date')->comment('تاريخ التحويل');

            // المخازن
            $table->foreignId('from_store_id')->constrained('stores')->onDelete('cascade')->comment('المخزن المُرسِل');
            $table->foreignId('to_store_id')->constrained('stores')->onDelete('cascade')->comment('المخزن المستقبِل');

            // الحالة والنوع
            $table->unsignedTinyInteger('status')->default(1)->comment('1=مسودة,2=معتمد,3=مستلم,4=ملغي');
            $table->unsignedTinyInteger('type')->default(1)->comment('1=تحويل عادي,2=تحويل استبدال');

            // الإجماليات
            $table->integer('total_items')->default(0)->comment('عدد الأصناف');
            $table->decimal('total_quantity', 15, 4)->default(0)->comment('إجمالي الكميات');
            $table->decimal('total_value', 15, 4)->default(0)->comment('إجمالي القيمة');

            // الموافقة والإرسال
            $table->foreignId('approved_by')->nullable()->constrained('users')->comment('المعتمد');
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('sent_by')->nullable()->constrained('users')->comment('المُرسِل');
            $table->timestamp('sent_at')->nullable();

            $table->unsignedBigInteger('journal_entry_id')->nullable();

            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['from_store_id', 'document_date']);
            $table->index(['to_store_id', 'document_date']);
            $table->index('status');
            $table->index('document_number');
        });

        // ========================================
        // جدول أصناف التحويلات الصادرة
        // ========================================
        Schema::create('st_transfer_out_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transfer_out_id')->constrained('st_transfer_outs')->onDelete('cascade');

                $table->unsignedBigInteger('product_id');
                   $table->unsignedBigInteger('unit_id');

            // الكميات والأسعار
            $table->decimal('quantity', 15, 4)->comment('الكمية المُرسلة');
            $table->decimal('unit_cost', 15, 4)->comment('تكلفة الوحدة');
            $table->decimal('total_cost', 15, 4)->comment('إجمالي التكلفة');

            // تفاصيل إضافية
            $table->boolean('have_sizes')->default(false)->comment('هل يحتوي على مقاسات');
            $table->text('unit');
            $table->unsignedTinyInteger('status')->default(1)->comment('1=مسودة,2=معتمد,3=مستلم,4=ملغي');

            $table->text('notes')->nullable();
            $table->timestamps();

            // منع التكرار لنفس الصنف في نفس التحويل
            $table->unique(['transfer_out_id', ], 'unique_transfer_out_item');
        });

        // ========================================
        // جدول التحويلات الواردة
        // ========================================
        Schema::create('st_transfer_ins', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('org_id');
            $table->unsignedBigInteger('branch_id');
            $table->foreignId('user_id')->constrained()->comment('المستخدم الذي استلم التحويل');

            // ربط بالتحويل الصادر الأصلي
            $table->foreignId('transfer_out_id')->nullable()->constrained('st_transfer_outs')->comment('التحويل الصادر المرتبط');

            // رقم وتاريخ المستند
            $table->string('document_number')->unique()->comment('رقم مستند الاستلام');
            $table->date('document_date')->comment('تاريخ الاستلام');
            $table->date('received_date')->nullable()->comment('تاريخ الاستلام الفعلي');

            // المخازن
            $table->foreignId('from_store_id')->constrained('stores')->comment('المخزن المُرسِل');
            $table->foreignId('to_store_id')->constrained('stores')->onDelete('cascade')->comment('المخزن المستقبِل');

            // الحالة والنوع

            $table->unsignedTinyInteger('type')->default(1)->comment('1=استلام عادي,2=استلام جزئي');
            $table->unsignedTinyInteger('status')->default(1)->comment('1=مسودة,2=معتمد,3=مستلم,4=ملغي');

            // الإجماليات
            $table->integer('total_items')->default(0)->comment('عدد الأصناف');
            $table->decimal('total_quantity', 15, 4)->default(0)->comment('إجمالي الكميات المستلمة');
            $table->decimal('total_value', 15, 4)->default(0)->comment('إجمالي القيمة');

            // الفروقات
            $table->decimal('variance_quantity', 15, 4)->default(0)->comment('فرق الكمية');
            $table->decimal('variance_value', 15, 4)->default(0)->comment('فرق القيمة');

            // الموافقة
            $table->foreignId('approved_by')->nullable()->constrained('users')->comment('المعتمد');
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('received_by')->nullable()->constrained('users')->comment('المستلم');
            $table->timestamp('received_at')->nullable();

            // القيد المحاسبي
            $table->foreignId('journal_entry_id')->nullable();

            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['from_store_id', 'document_date']);
            $table->index(['to_store_id', 'document_date']);
            $table->index('status');
            $table->index('transfer_out_id');
            $table->index('document_number');
        });

        // ========================================
        // جدول أصناف التحويلات الواردة
        // ========================================
        Schema::create('st_transfer_in_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transfer_in_id')->constrained('st_transfer_ins')->onDelete('cascade');
            $table->foreignId('transfer_out_item_id')->nullable()->constrained('st_transfer_out_items')->comment('صنف التحويل الصادر');


                $table->unsignedBigInteger('product_id');
                   $table->unsignedBigInteger('unit_id');

            // الكميات والأسعار
            $table->decimal('sent_quantity', 15, 4)->default(0)->comment('الكمية المُرسلة');
            $table->decimal('received_quantity', 15, 4)->comment('الكمية المستلمة فعلياً');
            $table->decimal('variance_quantity', 15, 4)->default(0)->comment('فرق الكمية');
            $table->decimal('unit_cost', 15, 4)->comment('تكلفة الوحدة');
            $table->decimal('total_cost', 15, 4)->comment('إجمالي التكلفة');

            // تفاصيل إضافية
            $table->boolean('have_sizes')->default(false)->comment('هل يحتوي على مقاسات');
            $table->text('unit');
            $table->unsignedTinyInteger('status')->default(1)->comment('1=مسودة,2=معتمد,3=مستلم,4=ملغي');

            // سبب الفرق في حال وجود
            $table->string('variance_reason')->nullable()->comment('سبب الفرق');

            $table->text('notes')->nullable();
            $table->timestamps();

            // منع التكرار لنفس الصنف في نفس التحويل
            $table->unique(['transfer_in_id'], 'unique_transfer_in_item');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('st_transfer_in_items');
        Schema::dropIfExists('st_transfer_ins');
        Schema::dropIfExists('st_transfer_out_items');
        Schema::dropIfExists('st_transfer_outs');
    }
};
