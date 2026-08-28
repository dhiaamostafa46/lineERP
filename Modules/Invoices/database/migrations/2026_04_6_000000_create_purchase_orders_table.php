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
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            // بيانات الفاتورة الأساسية
            $table->uuid('uuid')->unique();
            $table->integer('type_inv')->default(1)->comment('1:invoice,2:return');
            $table->string('invoice_number')->unique(); // الرقم التسلسلي الداخلي للمشتريات
            $table->string('supplier_invoice_number')->nullable(); // رقم فاتورة المورد (الورقية)
            $table->dateTime('issue_date'); // تاريخ ووقت الإصدار

            // أنواع الفواتير (للتوافق مع معايير الزكاة والضريبة)
            // 388: Tax Invoice, 389: Simplified Tax Invoice
            $table->string('invoice_type_code')->default('388');
            // 0100000: Standard, 0200000: Simplified
            $table->string('invoice_subtype_code')->default('0100000');

            // أطراف المعاملة
            $table->foreignId('supplier_id')->nullable()->constrained('inv_suppliers');
            $table->foreignId('store_id')->nullable()->constrained('stores')->onDelete('cascade');
            $table->foreignId('journal_entry_id')->nullable()->constrained('journal_entries');
            $table->foreignId('fiscal_year_id')->nullable()->constrained('fiscal_years');
            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->foreignId('branch_id')->nullable()->constrained('branches');
             $table->foreignId('cost_center_id')->nullable()->constrained('cost_centers');




            // البيانات المالية
            $table->decimal('total_exclusive_vat', 15, 4)->default(0); // الإجمالي غير شامل الضريبة
            $table->decimal('total_discount', 15, 4)->default(0);
            $table->integer('type_discount')->default(1)->comment('1:percent,2:fixed');
            $table->decimal('number_discount', 15, 4)->default(0);

                $table->text('notes')->nullable(); 
            $table->decimal('total_vat', 15, 4)->default(0);
            $table->decimal('total_inclusive_vat', 15, 4)->default(0); // المبلغ النهائي

            // بيانات تقنية (للحفاظ على نفس معايير المبيعات)
            $table->foreignId('parent_id')->nullable()->constrained('purchase_orders')->nullOnDelete(); // مرجع للفاتورة الأصلية في حال المرتجع
            $table->text('return_reason')->nullable(); // سبب الإرجاع (مطلوب للزكاة في الإشعارات المدينة)

             $table->string('attachment')->nullable();
            $table->integer('icv')->nullable()->index(); // Invoice Counter Value
            $table->text('previous_invoice_hash')->nullable();
            $table->text('qr_code')->nullable();
            $table->enum('status', ['new', 'approved', 'partially_received', 'completed', 'rejected'])->default('new');
            $table->text('technical_errors')->nullable();



            $table->boolean('is_locked')->default(false);
            $table->timestamp('locked_at')->nullable();
            $table->foreignId('locked_by')->nullable()->constrained('users')->nullOnDelete();



            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            $table->softDeletes();

            // فهارس البحث
            $table->index('issue_date');
            $table->index('supplier_id');
            $table->index('parent_id');
            $table->index('status');
        });





        Schema::create('purchase_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_order_id')->constrained('purchase_orders')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products');
            $table->string('product_name'); // حفظ الاسم وقت الشراء


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

            $table->index('purchase_order_id');



        });




        Schema::create('purchase_order_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_order_id')->constrained('purchase_orders')->cascadeOnDelete();
            // أكواد طرق الدفع (10: Cash, 30: Credit, 42: Bank Account, 48: Bank Card)
            $table->string('payment_method_code')->default('30');
            $table->foreignId('account_id')->nullable()->constrained('tree_accounts');
            $table->decimal('amount', 15, 4);
            $table->string('transaction_reference')->nullable(); // رقم الشبكة أو الحوالة
            $table->timestamps();

            $table->index('purchase_order_id');

        });

        // إضافة الربط بين الفواتير وأوامر الشراء بعد إنشاء الجداول
        Schema::table('purchase_invoices', function (Blueprint $table) {
            $table->foreign('from_po_id')->references('id')->on('purchase_orders')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_invoices', function (Blueprint $table) {
            $table->dropForeign(['from_po_id']);
        });
        Schema::dropIfExists('purchase_order_payments');
        Schema::dropIfExists('purchase_order_items');
        Schema::dropIfExists('purchase_orders');
    }
};
