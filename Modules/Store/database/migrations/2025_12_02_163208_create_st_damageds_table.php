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
        Schema::create('st_damageds', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('org_id');
            $table->unsignedBigInteger('branch_id');
            $table->foreignId('user_id')->constrained();
            // رقم المستند
            $table->string('document_number')->unique();
            $table->date('document_date');
            // المخزن
            $table->foreignId('store_id')->constrained('stores')->onDelete('cascade');
            // الحالة
            $table->unsignedTinyInteger('status')->default(1)->comment('0=draft,1=approved,2=processed,3=cancelled');
            $table->unsignedTinyInteger('type')->default(1);
            // الإجماليات
            $table->integer('total_items')->default(0)->comment('عدد الأصناف');
            $table->decimal('total_quantity', 15, 4)->default(0)->comment('إجمالي الكميات');
            $table->decimal('total_value', 15, 4)->default(0)->comment('إجمالي القيمة');
            // الموافقة
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamp('approved_at')->nullable();

            $table->text('notes')->nullable();
           $table->foreignId('journal_entry_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['store_id', 'document_date']);
            $table->index('status');
        });

        Schema::create('st_damaged_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('damaged_id')->constrained('st_damageds')->onDelete('cascade');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('unit_id');
            $table->boolean('have_sizes')->default(false);
            $table->decimal('quantity', 15, 4);
            $table->decimal('unit_cost', 15, 4);
            $table->decimal('total_cost', 15, 4);
            $table->text('unit');
            $table->unsignedTinyInteger('status')->default(1)->comment('0=draft,1=approved,2=processed,3=cancelled');
            // الإجماليات
            $table->text('notes')->nullable();
            $table->timestamps();
            // منع التكرار
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('st_damaged_items');
        Schema::dropIfExists('st_damageds');
    }
};
