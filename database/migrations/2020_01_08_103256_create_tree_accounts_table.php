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
        Schema::create('tree_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // 1-1-001

            // التصنيف - استخدام integer
            // 1=asset, 2=liability, 3=equity, 4=revenue, 5=expense, 6=cost_of_sales
            $table->unsignedTinyInteger('account_type')->default(1)->comment('1=أصول, 2=خصوم, 3=حقوق ملكية, 4=إيرادات, 5=مصروفات, 6=تكلفة المبيعات');

            // البنية الشجرية
            $table->foreignId('parent_id')->nullable()->constrained('tree_accounts')->nullOnDelete();

            $table->integer('level')->default(1); // مستوى الحساب في الشجرة
            $table->boolean('is_leaf')->default(true); // يقبل قيود

            // الحالة
            $table->boolean('status')->default(true);
            $table->boolean('is_system')->default(false);

            $table->json('attributes')->nullable();
            $table->boolean('use_cost_center')->default(false);

            // نوع الرصيد الافتتاحي - استخدام integer
            // 1=debit (مدين), 2=credit (دائن)
            $table->unsignedTinyInteger('type')->default(1)->comment('1=مدين, 2=دائن');

            $table->timestamps();
            $table->softDeletes();

            // فهارس
            $table->index(['parent_id', 'code']);
            $table->index('account_type');
            $table->index(['status', 'code'], 'idx_tree_accounts_status_code');
        });

        // ⭐ التغيير هنا: استخدام tree_accounts_id بدلاً من tree_account_id
        Schema::create('tree_account_translations', function (Blueprint $table) {
            $table->id();
            // ⭐ لاحظ: tree_accounts_id (بالجمع مع s)
            $table->foreignId('tree_accounts_id')->constrained('tree_accounts')->onDelete('cascade');
            $table->string('locale')->index();
            $table->string('name');
            $table->text('description')->nullable();
            // ⭐ تحديث الـ unique constraint أيضاً
            $table->unique(['tree_accounts_id', 'locale']);
            $table->index(['locale', 'name'], 'idx_tree_account_trans_locale_name');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tree_account_translations');
        Schema::dropIfExists('tree_accounts');
    }
};
