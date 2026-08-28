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
        Schema::create('journal_entries', function (Blueprint $table) {
            $table->id();
            $table->string('entry_number')->unique(); // JE-2024-0001
            $table->date('entry_date'); // تاريخ القيد
            $table->text('description')->nullable(); // البيان

            $table->foreignId('fiscal_year_id')->constrained('fiscal_years')->cascadeOnDelete();
            // نوع القيد
            $table->unsignedTinyInteger('entry_type')->default(1)->comment('1=manual, 2=opening, 3=closing, 4=depreciation, 5=adjustment');
            $table->unsignedTinyInteger('status')->default(1)->comment('1=draft, 2=posted, 3=reversed');

            $table->decimal('total_debit', 15, 2)->default(0);
            $table->decimal('total_credit', 15, 2)->default(0);
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('posted_by')->nullable()->constrained('users');
            $table->timestamp('posted_at')->nullable();
            
            $table->boolean('is_locked')->default(false);
            $table->timestamp('locked_at')->nullable();
            $table->foreignId('locked_by')->nullable()->constrained('users')->nullOnDelete();


            // مرجع القيد (فاتورة، سند...)
            $table->string('reference_type')->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->string('attachment')->nullable();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            // بحث برقم القيد
            $table->index('entry_number');
            // تقارير الفترات + السنة + الفرع
            $table->index(['fiscal_year_id', 'entry_date', 'branch_id']);
            // أغلب التقارير على القيود المرحلة
            $table->index(['status', 'entry_date']);
            // تقارير نوع القيد (إهلاك – افتتاحي – ختامي)
            $table->index(['entry_type', 'status']);
            // تقارير الفروع
            $table->index('branch_id');
            // الربط مع المستندات (فاتورة / سند)
            $table->index(['reference_id', 'reference_type']);
        });

        Schema::create('journal_entry_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('journal_entry_id')->constrained('journal_entries')->cascadeOnDelete();
            $table->foreignId('tree_account_id')->constrained('tree_accounts')->cascadeOnDelete();
            $table->foreignId('cost_center_id')->nullable()->constrained('cost_centers')->nullOnDelete();
            $table->decimal('debit', 15, 2)->default(0); // مدين
            $table->decimal('credit', 15, 2)->default(0); // دائن
            $table->text('description')->nullable();
            $table->boolean('is_locked')->default(false);
            $table->timestamp('locked_at')->nullable();
            $table->foreignId('locked_by')->nullable()->constrained('users')->nullOnDelete(); // وصف السطر
            $table->timestamps();
            $table->index('journal_entry_id');
            // دفتر الأستاذ
            $table->index(['tree_account_id', 'journal_entry_id']);
            // تقارير مراكز التكلفة
            $table->index(['tree_account_id', 'cost_center_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('journal_entry_details');
        Schema::dropIfExists('journal_entries');
    }
};
