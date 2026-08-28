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
        Schema::create('fnc_bonds', function (Blueprint $table) {
            $table->id();
            $table->string('voucher_number')->unique(); // رقم السند
            $table->date('date'); // تاريخ السند
            $table->tinyInteger('bond_type')->default(1)->comment("1=payment (صرف), 2=receipt (قبض)");

            $table->decimal('amount', 15, 2);
            $table->string('reference_number')->nullable();
            // الحساب المالي (الصندوق أو البنك) - الطرف الثابت حسب نوع السند
            $table->foreignId('fund_account_id')->constrained('tree_accounts')->comment('حساب الصندوق أو البنك');
            // الحساب المقابل (العميل، المورد، المصروف، إلخ)
            $table->foreignId('contact_account_id')->constrained('tree_accounts')->comment('الحساب المقابل');
            $table->foreignId('cost_center_id')->nullable()->constrained('cost_centers')->nullOnDelete();

            $table->foreignId('fiscal_year_id')->constrained('fiscal_years')->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();

            $table->text('description')->nullable();
            $table->tinyInteger('status')->default(1)->comment("1=draft, 2=approved, 3=cancelled");
            $table->string('attachment')->nullable();

            $table->foreignId('journal_entry_id')->nullable()->constrained('journal_entries')->nullOnDelete();


            $table->foreignId('user_id')->nullable()->constrained('users');

            // حقول التدقيق والإقفال
            $table->boolean('is_locked')->default(false);
            $table->timestamp('locked_at')->nullable();
            $table->foreignId('locked_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users');

            $table->timestamps();
            $table->softDeletes();

            // الفهارس (Indexes) لتحسين أداء البحث والتقارير
            $table->index('date'); // البحث بالتاريخ
            $table->index('status'); // البحث بالحالة
            $table->index(['bond_type', 'date']); // تقارير المقبوضات/المصروفات لفترة
            $table->index(['branch_id', 'date']); // تقارير الفروع
            $table->index(['fund_account_id', 'date']); // كشف حساب الصندوق/البنك
            $table->index(['contact_account_id', 'date']); // كشف حساب الطرف المقابل
            $table->index('fiscal_year_id'); // البحث بالسنة المالية
            $table->index('journal_entry_id'); // تتبع القيد المحاسبي
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fnc_bonds');
    }
};
