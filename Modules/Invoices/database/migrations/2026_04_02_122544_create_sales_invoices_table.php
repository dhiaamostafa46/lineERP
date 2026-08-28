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
        Schema::create('sales_invoices', function (Blueprint $table) {
            $table->id();
            // بيانات الفاتورة الأساسية
            $table->uuid('uuid')->unique();
            $table->integer('type_inv')->default(1)->comment('1:invoice,2:return');
            // مطلوب للمرحلة الثانية
            $table->string('invoice_number')->unique(); // الرقم التسلسلي للفاتورة
            $table->dateTime('issue_date'); // تاريخ ووقت الإصدار (مطلوب بالوقت)

            // أنواع الفواتير حسب متطلبات الزكاة
            // 388: Tax Invoice (B2B), 389: Simplified Tax Invoice (B2C)
            $table->string('invoice_type_code')->default('388');
            // 0100000: Standard, 0200000: Simplified
            $table->string('invoice_subtype_code')->default('0100000');

            // أطراف المعاملة
            $table->foreignId('customer_id')->nullable()->constrained('inv_customers');
            $table->foreignId('branch_id')->nullable()->constrained('branches');

            $table->foreignId('store_id')->nullable()->constrained('stores')->onDelete('cascade');
            $table->foreignId('journal_entry_id')->nullable()->constrained('journal_entries');
            $table->foreignId('fiscal_year_id')->nullable()->constrained('fiscal_years');
            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->foreignId('cost_center_id')->nullable()->constrained('cost_centers');

            // البيانات المالية
            $table->decimal('total_exclusive_vat', 15, 4)->default(0);
            // الإجمالي غير شامل الضريبة
            $table->decimal('total_discount', 15, 4)->default(0);
            $table->integer('type_discount')->default(1)->comment('1:percent,2:fixed');
            $table->decimal('number_discount', 15, 4)->default(0);

            $table->decimal('total_vat', 15, 4)->default(0);
            $table->decimal('total_inclusive_vat', 15, 4)->default(0); // المبلغ النهائي

            $table->text('notes')->nullable();
            $table->boolean('is_locked')->default(false);
            $table->timestamp('locked_at')->nullable();
            $table->foreignId('locked_by')->nullable()->constrained('users')->nullOnDelete();

            // بيانات تقنية للمرحلة الثانية (Integration)
            $table->foreignId('parent_id')->nullable()->constrained('sales_invoices')->nullOnDelete(); // مرجع للفاتورة الأصلية في حال المرتجع
            $table->text('return_reason')->nullable(); // سبب الإرجاع (مطلوب للزكاة في الإشعارات الدائنة)

            $table->integer('icv')->nullable()->index(); // Invoice Counter Value (عداد الفواتير)
            $table->text('previous_invoice_hash')->nullable(); // الهاش الخاص بالفاتورة السابقة
            $table->text('qr_code')->nullable(); // كود الـ QR المولد
            $table->integer('status')->default(1)->comment('1: Draft, 2: Submitted, 3: Reported, 4: Cleared, 5: Rejected, 6: Returned, 7: Partially Returned');
            $table->text('zatca_errors')->nullable();

            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            $table->softDeletes();

            // فهارس البحث
            $table->index('issue_date');
            $table->index('customer_id');
            $table->index('parent_id');
            $table->index('status');
        });

        Schema::create('sales_invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sales_invoice_id')->constrained('sales_invoices')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products');
            $table->string('product_name'); // حفظ الاسم وقت البيع

            $table->unsignedBigInteger('unit_id');
            $table->boolean('have_sizes')->default(false);

            $table->decimal('quantity', 15, 4);
            $table->decimal('unit_price', 15, 4);
            $table->decimal('total_discount', 15, 4)->default(0);
            $table->integer('type_discount')->default(1)->comment('1:percent,2:fixed');
            $table->decimal('number_discount', 15, 4)->default(0);

            $table->decimal('vat_rate', 5, 2); // نسبة الضريبة (مثلاً 15.00)
            $table->decimal('vat_amount', 15, 4);
            $table->decimal('subtotal_with_vat', 15, 4); // إجمالي السطر شامل الضريبة

            $table->timestamps();

            $table->index('sales_invoice_id');
        });

        Schema::create('sales_invoice_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sales_invoice_id')->constrained('sales_invoices')->cascadeOnDelete();

            // أكواد طرق الدفع حسب الزكاة (10: Cash, 30: Credit, 42: Bank Account, 48: Bank Card)
            $table->string('payment_method_code')->default('30');
            $table->foreignId('account_id')->nullable()->constrained('tree_accounts');
            $table->decimal('amount', 15, 4);
            $table->string('transaction_reference')->nullable(); // رقم الشبكة أو الحوالة

            $table->timestamps();

            $table->index('sales_invoice_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_invoice_payments');
        Schema::dropIfExists('sales_invoice_items');
        Schema::dropIfExists('sales_invoices');
    }
};
