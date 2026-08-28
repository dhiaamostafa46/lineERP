<?php

/**
 * =========================
 * Migration: Assets & Depreciation
 * =========================
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('assets', function (Blueprint $table) {
            $table->id();

            // بيانات تعريفية
            $table->string('code')->unique()->comment('AST-0001');
            $table->foreignId('asset_account_id')->constrained('tree_accounts')->comment('حساب الأصل');
            // $table->foreignId('category_id')->nullable()->constrained('asset_categories');
            // $table->foreignId('location_id')->nullable()->constrained('locations');

            // بيانات الشراء
            $table->date('purchase_date');
            $table->decimal('purchase_value', 15, 2);
            $table->integer('useful_life')->comment('العمر الإنتاجي بالأشهر');
            $table->decimal('salvage_value', 15, 2)->default(0)->comment('القيمة التخريدية');

            // حسابات الإهلاك
            $table->foreignId('depreciation_expense_account_id')->constrained('tree_accounts')->comment('حساب مصروف الإهلاك');
            $table->foreignId('accumulated_depreciation_account_id')->constrained('tree_accounts')->comment('حساب مجمع الإهلاك');

            // طريقة الإهلاك
            $table->unsignedTinyInteger('depreciation_method')->default(1)->comment("1: straight_line, 2: declining_balance, 3: sum_of_years, 4: units_of_production");
            $table->decimal('declining_rate', 5, 2)->nullable()->comment('معدل الإهلاك المتناقص %');

            // الحالة
            $table->unsignedTinyInteger('status')->default(1)->comment("1: active, 2: disposed, 3: fully_depreciated, 4: under_maintenance");

            // بيانات الاستبعاد
            $table->date('disposal_date')->nullable();
            $table->decimal('disposal_value', 15, 2)->nullable()->comment('قيمة البيع/الاستبعاد');
            $table->unsignedTinyInteger('disposal_type')->nullable()->comment("1: sale, 2: scrap, 3: exchange, 4: donation, 5: lost");
            $table->foreignId('disposal_journal_entry_id')->nullable()->constrained('journal_entries')->nullOnDelete();
            $table->decimal('disposal_gain_loss', 15, 2)->nullable()->comment('ربح أو خسارة الاستبعاد');

            // إحصائيات
            $table->decimal('total_depreciation', 15, 2)->default(0)->comment('إجمالي الإهلاك المحتسب');
            $table->decimal('current_book_value', 15, 2)->nullable()->comment('القيمة الدفترية الحالية');

            // تواريخ مهمة
            $table->date('last_depreciation_date')->nullable();
            $table->date('next_depreciation_date')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            // $table->index(['asset_account_id']);
            $table->index(['status', 'purchase_date']);
            // $table->index(['category_id', 'status']);
            $table->index(['next_depreciation_date']);
        });

        Schema::create('asset_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained('assets')->cascadeOnDelete();
            $table->string('locale', 5)->index();
            $table->string('name');
            $table->text('description')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['asset_id', 'locale']);
            $table->index(['locale', 'name']);
        });

        Schema::create('depreciations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained('assets')->cascadeOnDelete();

            // الفترة المحاسبية
            $table->year('year');
            $table->unsignedTinyInteger('month');
            $table->date('period_date')->comment('آخر يوم في الشهر');

            // قيم الإهلاك
            $table->decimal('depreciation_amount', 15, 2)->comment('قسط الإهلاك للفترة');
            $table->decimal('accumulated_depreciation', 15, 2)->comment('مجمع الإهلاك حتى الآن');
            $table->decimal('book_value', 15, 2)->comment('القيمة الدفترية');

            // القيد المحاسبي
            $table->foreignId('journal_entry_id')->nullable()->constrained('journal_entries')->nullOnDelete();

            // نوع القيد
            $table->unsignedTinyInteger('entry_type')->default(1)->comment("1: monthly, 2: disposal, 3: adjustment, 4: reversal");

            // الحالة
            $table->boolean('is_posted')->default(false)->comment('تم الترحيل');
            $table->boolean('is_locked')->default(false)->comment('مقفل');
            $table->boolean('is_reversed')->default(false)->comment('تم العكس');

            $table->text('notes')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->unique(['asset_id', 'year', 'month']);
            $table->index(['year', 'month']);
            $table->index(['is_posted', 'period_date']);
            $table->index(['entry_type', 'is_posted']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('depreciations');
        Schema::dropIfExists('asset_translations');
        Schema::dropIfExists('assets');
    }
};
